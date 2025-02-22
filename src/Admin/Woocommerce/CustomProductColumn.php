<?php

declare(strict_types=1);

namespace LPTS\Admin\Woocommerce;

use LPTS\Api\ApiManager;

/**
 * Add custom column to product list
 *
 * @package LPTS\Admin\Woocommerce
 * @since   1.0.0
 */
class CustomProductColumn
{
    public ?array $sendinblue_lists;

    public function __construct()
    {
        $this->sendinblue_lists = ApiManager::getLists();

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
            $product_list_id = get_post_meta($product_id, '_lpts_list', true);

            // we check if $product_list_id exist in $this->sendinblue_lists
            // if true we echo the value(list name).
            if (isset($this->sendinblue_lists[$product_list_id])) {
                $list = $this->sendinblue_lists[$product_list_id];

                echo esc_html($list);
            } else {
                echo '–';
            }
        }
    }
}
