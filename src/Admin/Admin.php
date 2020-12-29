<?php

namespace WcProToSL\Admin;

use WcProToSL\Admin\Woocommerce\CustomProductColumn;
use WcProToSL\Admin\Woocommerce\CustomProductField;
use WcProToSL\Admin\Woocommerce\PaymentComplete;

class Admin
{
    public string $request_uri;
    public array $array;

    public function __construct()
    {
        $this->request_uri = $_SERVER['REQUEST_URI'];
        $this->array = explode('/', $this->request_uri);

        add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);

        new WcProToSL_Settings();
        new CustomProductField();
        new PaymentComplete();
        new CustomProductColumn();
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        if (str_contains($this->array[3], 'woocommerce_product_to_sendinblue_list')) {
            wp_enqueue_style(
                'wcprotosl_bootstrap',
                WCPROTOSL_URL . 'assets/vendor/bootstrap/css/bootstrap.min.css',
                [],
                WCPROTOSL_VERSION,
                'all'
            );

            wp_enqueue_style(
                'woocommerce-product-to-sendinblue-list',
                WCPROTOSL_URL . 'assets/admin/css/admin-app.css',
                [],
                WCPROTOSL_VERSION,
                'all'
            );
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        if (str_contains($this->array[3], 'woocommerce_product_to_sendinblue_list')) {
            wp_enqueue_script(
                'woocommerce-product-to-sendinblue-list',
                WCPROTOSL_URL . 'assets/admin/js/admin-app.js',
                ['jquery'],
                WCPROTOSL_VERSION,
                false
            );
        }

    }
}