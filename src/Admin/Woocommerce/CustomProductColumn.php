<?php

namespace LPTS\Admin\Woocommerce;

use LPTS\Api\ApiManager;

/**
 * Class CustomProductColumn
 *
 * @package LPTS\Admin\Woocommerce
 * @since   1.0.0
 */
class CustomProductColumn
{
    public $sendinblue_lists;

    public function __construct()
    {
        $this->sendinblue_lists = ApiManager::getLists();

        add_filter('manage_edit-product_columns', [$this, 'productsListColumn'], 9999);
        add_action('manage_product_posts_custom_column', [$this, 'productsListColumnContent'], 10, 2);
    }

    /**
     * Create product column
     *
     * @param $columns
     *
     * @return mixed
     * @since 1.0.0
     */
    public function productsListColumn($columns)
    {
        $columns['list'] = __('List', LPTS_TEXT_DOMAIN);

        return $columns;
    }

    /**
     * Add content to product column
     *
     * @param $column
     * @param $product_id
     *
     * @since 1.0.0
     */
    public function productsListColumnContent($column, $product_id)
    {
        if ($column == 'list') {
            $product_list_id = get_post_meta($product_id, '_lpts_list', true);

            // we check if $product_list_id exist in $this->sendinblue_lists
            // if true we echo the value(list name)
            if (isset($this->sendinblue_lists[$product_list_id])) {
                $list = $this->sendinblue_lists[$product_list_id];

                echo $list;
            } else {
                echo '-';
            }
        }
    }
}
