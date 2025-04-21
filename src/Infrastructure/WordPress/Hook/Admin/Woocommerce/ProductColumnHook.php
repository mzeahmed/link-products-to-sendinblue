<?php

declare(strict_types=1);

namespace LPTS\Infrastructure\WordPress\Hook\Admin\Woocommerce;

use LPTS\Application\Contract\HookInterface;
use LPTS\Domain\Services\Admin\Woocommerce\ProductColumnService;

/**
 * @since 2.0.0
 */
class ProductColumnHook implements HookInterface
{
    public function __construct(
        private ProductColumnService $productColumnService
    ) {}

    /**
     * @inheritDoc
     */
    public function register()
    {
        add_filter('manage_edit-product_columns', [$this, 'productsListColumn'], 9999);
        add_action('manage_product_posts_custom_column', [$this, 'productsListColumnContent'], 10, 2);
    }

    /**
     * Create product column
     *
     * @param array $columns list of columns.
     *
     * @return array
     * @since 1.0.0
     */
    public function productsListColumn(array $columns)
    {
        return $this->productColumnService->productsListColumn($columns);
    }

    /**
     * Add content to product column
     *
     * @param string $column column name.
     * @param int $product_id product id.
     *
     * @since 1.0.0
     */
    public function productsListColumnContent(string $column, int $product_id)
    {
        $this->productColumnService->productsListColumnContent($column, $product_id);
    }
}
