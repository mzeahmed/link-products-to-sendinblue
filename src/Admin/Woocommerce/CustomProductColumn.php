<?php

namespace Wc_Sendinblue_Synchronize\Admin\Woocommerce;

use Wc_Sendinblue_Synchronize\Api\Api;

class CustomProductColumn
{
    public array $sendinblue_lists;

    public function __construct()
    {
        $this->sendinblue_lists = Api::get_lists();

        add_filter('manage_edit-product_columns', [$this, 'products_list_column'], 9999);
        add_action('manage_product_posts_custom_column', [$this, 'products_list_column_content'], 10, 2);
    }

    /**
     * Create product column
     *
     * @param $columns
     *
     * @return mixed
     */
    public function products_list_column($columns)
    {
        $columns['list'] = __('List', WC_SS_TEXT_DOMAIN);

        return $columns;
    }

    /**
     * Add content to product column
     *
     * @param $column
     * @param $product_id
     */
    public function products_list_column_content($column, $product_id)
    {
        if ($column == 'list') {
            $product_list_id = get_post_meta($product_id, '_wc_sendinblue_synchronize_list', true);

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