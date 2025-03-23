<?php

declare(strict_types=1);

namespace LPTS\Infrastructure\WordPress\Hook\Admin\Woocommerce;

use LPTS\Shared\Enums\MetaKey;
use LPTS\Application\Contract\HookInterface;
use LPTS\Infrastructure\External\Brevo\ApiManager;

/**
 * @since 1.2.0
 */
class CustomProductColumnHook implements HookInterface
{
    public ?array $brevoLists;

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->brevoLists = ApiManager::getLists();

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
    public function productsListColumn(array $columns): array
    {
        $columns['list'] = __('Brevo list', 'link-products-to-sendinblue');

        return $columns;
    }

    /**
     * Add content to product column
     *
     * @param string $column column name.
     * @param int $product_id product id.
     *
     * @since 1.0.0
     */
    public function productsListColumnContent(string $column, int $product_id): void
    {
        if ('list' === $column) {
            $productListIds = get_post_meta($product_id, Metakey::PRODUCT_LIST->value, true);

            if (empty($productListIds)) {
                echo '–';

                return;
            }

            // we check if $product_list_id exist in $this->sendinblue_lists
            // if true we echo the value(list name).
            foreach ($productListIds as $row) {
                if (isset($this->brevoLists[$row['list_id']])) {
                    $list = $this->brevoLists[$row['list_id']];

                    echo esc_html($list);
                } else {
                    echo '–';
                }
            }
        }
    }
}
