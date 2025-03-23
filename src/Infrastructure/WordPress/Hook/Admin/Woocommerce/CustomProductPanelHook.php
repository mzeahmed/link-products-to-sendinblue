<?php

declare(strict_types=1);

namespace LPTS\Infrastructure\WordPress\Hook\Admin\Woocommerce;

use LPTS\Shared\Utils\Utils;
use LPTS\Shared\Enums\MetaKey;
use LPTS\Infrastructure\View\Renderer;
use LPTS\Application\Contract\HookInterface;
use LPTS\Infrastructure\External\Brevo\ApiManager;

/**
 * @since 1.2.0
 */
class CustomProductPanelHook implements HookInterface
{
    public ?array $lists;
    public string|bool $api_key;

    public function __construct(
        private Renderer $renderer
    ) {
        $this->lists = ApiManager::getLists();
        $this->api_key = Utils::getApiKey();
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        // push 'Select a list' to $this->list.
        $this->lists[] = __('Select a list', 'link-products-to-sendinblue');

        // we sort sendinblue list by key(id) in reverse order, to add 'Select a list' as first element of the array.
        krsort($this->lists);

        if (!empty($this->api_key)) {
            add_filter('woocommerce_product_data_tabs', [$this, 'customProductDataTab']);
            add_action('woocommerce_product_data_panels', [$this, 'productDataPanelRender']);
            add_action('woocommerce_process_product_meta', [$this, 'saveProductMeta']);
        }
    }

    /**
     * Add new tabs
     *
     * @param array $tabs list of tabs
     *
     * @return array
     * @since 1.0.0
     */
    public function customProductDataTab(array $tabs): array
    {
        $tabs['sendinblue'] = [
            'label' => __('Brevo (ex Sendinblue)', 'link-products-to-sendinblue'),
            'target' => 'sendinblue_data_panel',
            'class' => ['hide_if_external'],
            'priority' => 100,
        ];

        return $tabs;
    }

    /**
     * Display tab view
     *
     * @return void
     * @since 1.0.0
     */
    public function productDataPanelRender(): void
    {
        $listIds = get_post_meta(get_the_ID(), Metakey::PRODUCT_LIST->value) ?: [];
        $roles = wp_roles()->get_names();

        echo $this->renderer->render('admin/woocommerce/product-sendinblue-panel', [
            'lists' => $this->lists,
            'listIds' => array_shift($listIds),
            'roles' => $roles,
        ]);
    }

    /**
     * Saving field in the database
     *
     * @param int $postId Id of product.
     *
     * @since 1.0.0
     */
    public function saveProductMeta(int $postId): void
    {
        $product = wc_get_product($postId);
        $rawEntries = $_POST['_selec_list'] ?? [];

        $product->update_meta_data(Metakey::PRODUCT_LIST->value, $rawEntries);

        $product->save();
    }
}
