<?php

declare(strict_types=1);

namespace LPTS\Infrastructure\WordPress\Hook\Admin\Woocommerce;

use LPTS\Shared\Utils\Utils;
use LPTS\Application\Contract\HookInterface;
use LPTS\Domain\Services\Admin\Woocommerce\ProductPanelService;

/**
 * @since 2.0.0
 */
class ProductPanelHook implements HookInterface
{
    private string|bool $apiKey;

    public function __construct(
        private ProductPanelService $productPanelService,
    ) {
        $this->apiKey = Utils::getApiKey();
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        if (!empty($this->apiKey)) {
            add_filter('woocommerce_product_data_tabs', [$this, 'customProductDataTab']);
            add_action('woocommerce_product_data_panels', [$this, 'productDataPanelRender']);
            add_action('woocommerce_process_product_meta', [$this, 'saveProductMeta']);

            add_action('woocommerce_product_after_variable_attributes', [$this, 'variationListField'], 10, 3);
            add_action('woocommerce_save_product_variation', [$this, 'saveVariationLists'], 10, 2);
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
