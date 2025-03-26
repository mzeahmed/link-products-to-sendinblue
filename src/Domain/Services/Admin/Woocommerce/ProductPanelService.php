<?php

declare(strict_types=1);

namespace LPTS\Domain\Services\Admin\Woocommerce;

use LPTS\Shared\Enums\MetaKey;
use LPTS\Infrastructure\View\Renderer;
use LPTS\Infrastructure\External\Brevo\BrevoManager;

/**
 * @since 2.0.0
 */
class ProductPanelService
{
    public ?array $lists;

    public function __construct(
        private Renderer $renderer
    ) {
        $this->lists = BrevoManager::getLists();

        $this->lists[] = __('Select a list', 'link-products-to-sendinblue');

        // we sort Brevo list by key(id) in reverse order, to add 'Select a list' as first element of the array.
        krsort($this->lists);
    }

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

    public function productDataPanelRender(): void
    {
        $product = wc_get_product(get_the_ID());
        $isVariable = $product && $product->is_type('variable');

        $listIds = get_post_meta(get_the_ID(), Metakey::PRODUCT_LIST->value) ?: [];
        $roles = wp_roles()->get_names();

        echo $this->renderer->render('admin/woocommerce/product-sendinblue-panel', [
            'lists' => $this->lists,
            'listIds' => array_shift($listIds),
            'roles' => $roles,
            'isVariable' => $isVariable,
        ]);
    }

    public function saveProductMeta(int $postId): void
    {
        $rawEntries = $_POST[Metakey::PRODUCT_LIST->value] ?? [];

        $product = wc_get_product($postId);
        $product->update_meta_data(Metakey::PRODUCT_LIST->value, $rawEntries);

        $product->save();
    }

    public function variationListField($loop, $variationData, $variation): void
    {
        $saved = get_post_meta($variation->ID, Metakey::VARIATION_PRODUCT_LISTS->value, true);

        echo $this->renderer->render('admin/woocommerce/variation-product-panel', [
            'lists' => $this->lists,
            'saved' => $saved,
            'loop' => $loop,
        ]);
    }

    public function saveVariationLists(int $variationId, int $i): void
    {
        $selected = sanitize_text_field($_POST[Metakey::VARIATION_PRODUCT_LISTS->value][$i] ?? '');

        // dump($variationId);

        $variationProduct = wc_get_product($variationId);
        $variationProduct->update_meta_data(Metakey::VARIATION_PRODUCT_LISTS->value, $selected);

        $variationProduct->save();
    }
}
