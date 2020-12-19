<?php

namespace WcProToSL\Admin\Woocommerce;

use WcProToSL\Admin\Options;
use WcProToSL\Api\ApiManager;
use WcProToSL\Renderer\Renderer;

/**
 * Class CustomProductField
 *
 * @package WcProToSL\Admin\Woocommerce
 */
class CustomProductField
{
    public array $lists;
    public $main_option;

    public function __construct()
    {
        $this->lists = ApiManager::get_lists();
        $this->main_option = get_option(Options::WCPROTOSL_MAIN_OPTION_NAME);

        // push 'Select a list' to $this->list
        array_push($this->lists, __('Select a list', WCPROTOSL_TEXT_DOMAIN));

        // we sort sendinblue list by key(id) in reverse order, to add 'Select a list' as first element of the array
        krsort($this->lists);

        if (isset($this->main_option['access_key'])) {
            add_filter('woocommerce_product_data_tabs', [$this, 'custom_product_data_tab']);
            add_action('woocommerce_product_data_panels', [$this, 'product_data_panel_render']);
            add_action('woocommerce_process_product_meta', [$this, 'process_product_meta']);
        }
    }

    /**
     * Add new tab
     *
     * @param $tabs
     *
     * @return mixed
     * @since 1.0.0
     */
    public function custom_product_data_tab($tabs)
    {
        $tabs['sendinblue'] = [
            'label' => __('Sendinblue', WCPROTOSL_TEXT_DOMAIN),
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
    public function product_data_panel_render(): string
    {
        $value = get_post_meta(get_the_ID(), '_wcprotosl_list') ?: '';

        return Renderer::render(
            'admin/woocommerce/product-sendinblue-panel',
            [
                'lists' => $this->lists,
                'value' => $value,
            ]
        );
    }

    /**
     * Saving field in the database
     *
     * @param $post_id
     *
     * @since 1.0.0
     */
    public function process_product_meta($post_id)
    {
        $product = wc_get_product($post_id);

        $product->update_meta_data('_wcprotosl_list', sanitize_text_field($_POST['_selec_list']));

        $product->save();
    }
}