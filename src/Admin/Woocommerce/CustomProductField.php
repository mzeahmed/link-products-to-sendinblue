<?php

declare(strict_types=1);

namespace LPTS\Admin\Woocommerce;

use LPTS\View\View;
use LPTS\Api\ApiManager;

/**
 * Add a custom field to the product page
 *
 * @package LPTS\Admin\Woocommerce
 * @since   1.0.0
 */
class CustomProductField
{
    public ?array $lists;
    public string|bool $api_key;

    public function __construct()
    {
        $this->lists = ApiManager::getLists();
        $this->api_key = get_option(LPTS_API_KEY_V3_OPTION);

        // push 'Select a list' to $this->list.
        $this->lists[] = __('Select a list', 'link-products-to-sendinblue');

        // we sort sendinblue list by key(id) in reverse order, to add 'Select a list' as first element of the array.
        krsort($this->lists);

        if (!empty($this->api_key)) {
            add_filter('woocommerce_product_data_tabs', [$this, 'customProductDataTab']);
            add_action('woocommerce_product_data_panels', [$this, 'productDataPanelRender']);
            add_action('woocommerce_process_product_meta', [$this, 'processProductMeta']);
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
     * @return string
     * @since 1.0.0
     */
    public function productDataPanelRender(): string
    {
        $value = get_post_meta(get_the_ID(), '_lpts_list') ?: [];

        return View::render('admin/woocommerce/product-sendinblue-panel', [
            'lists' => $this->lists,
            'value' => $value,
        ]);
    }

    /**
     * Saving field in the database
     *
     * @param int $postId Id of product.
     *
     * @since 1.0.0
     */
    public function processProductMeta(int $postId): void
    {
        // $product = wc_get_product($postId);
        // $product->update_meta_data('_lpts_list', sanitize_text_field($_POST['_selec_list']));
        // $product->save();

        // $product = wc_get_product($postId);
        //
        // if (!empty($_POST['_selec_list']) && is_array($_POST['_selec_list'])) {
        //     $lists = array_map('sanitize_text_field', $_POST['_selec_list']);
        // } else {
        //     $lists = [];
        // }
        //
        // $product->update_meta_data('_lpts_list', $lists);
        // $product->save();

        $product = wc_get_product($postId);
        $rawEntries = $_POST['_selec_list'] ?? [];

        $formatted = array_map(static function ($entry) {
            return [
                'list_id' => sanitize_text_field($entry['list_id'] ?? ''),
                'condition' => sanitize_text_field($entry['condition'] ?? 'always'),
                'param' => sanitize_text_field($entry['param'] ?? ''),
            ];
        }, $rawEntries);

        $product->update_meta_data('_lpts_list', $formatted);
        $product->save();
    }
}
