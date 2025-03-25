<?php

declare(strict_types=1);

namespace LPTS\Infrastructure\WordPress\Hook\Admin\Woocommerce;

use LPTS\Shared\Enums\MetaKey;
use LPTS\Shared\Enums\OptionKey;
use LPTS\Application\Contract\HookInterface;
use LPTS\Domain\Services\Admin\Woocommerce\ProductPanelService;

/**
 * @since 1.2.0
 */
class ProductPanelHook implements HookInterface
{
    public function __construct(
        private ProductPanelService $productPanelService,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->lists[] = __('Select a list', 'link-products-to-sendinblue');

        // we sort Brevo list by key(id) in reverse order, to add 'Select a list' as first element of the array.
        krsort($this->lists);

        if (!empty($this->apiKey)) {
            add_filter('woocommerce_product_data_tabs', [$this, 'customProductDataTab']);
            add_action('woocommerce_product_data_panels', [$this, 'productDataPanelRender']);
            add_action('woocommerce_process_product_meta', [$this, 'saveProductMeta']);

            add_action('woocommerce_product_after_variable_attributes', [$this, 'variationListField'], 10, 3);
            add_action('woocommerce_save_product_variation', [$this, 'saveVariationLists'], 10, 2);

            /** @todo supprimmer une fois les testes validés */
            add_action('admin_init', function () {
                $mainOption = get_option(OptionKey::MAIN_OPTION->value);
                $clientMatchedAttributes = $mainOption['client_matched_attributes'];

                $orderId = 2549757;

                $order = wc_get_order($orderId);
                $email = $order->get_billing_email();

                $info = [];
                if (isset($clientMatchedAttributes)) {
                    foreach ($clientMatchedAttributes as $contactAttr => $customerAttr) {
                        $info[$contactAttr] = $order->$customerAttr;
                    }
                }

                $items = $order->get_items();

                foreach ($items as $item) {
                    $productId = $item->get_product_id();
                    $variationId = $item->get_variation_id();
                    // 2549750

                    // dump([
                    //     'product_id' => $item->get_product_id(),
                    //     'variation_id' => $item->get_variation_id(),
                    //     'product_type' => $item->get_product()?->get_type(),
                    // ]);

                    $product = wc_get_product($productId);

                    $listId = get_post_meta($variationId, Metakey::VARIATION_PRODUCT_LISTS->value, true);
                    // dd($product->is_type('variable'));
                }
            });
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
        return $this->productPanelService->customProductDataTab($tabs);
    }

    /**
     * Display tab view
     *
     * @return void
     * @since 1.0.0
     */
    public function productDataPanelRender(): void
    {
        $this->productPanelService->productDataPanelRender();
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
        $this->productPanelService->saveProductMeta($postId);
    }

    public function variationListField($loop, $variationData, $variation): void
    {
        $this->productPanelService->variationListField($loop, $variationData, $variation);
    }

    public function saveVariationLists(int $variationId, int $i): void
    {
        $this->productPanelService->saveVariationLists($variationId, $i);
    }
}
