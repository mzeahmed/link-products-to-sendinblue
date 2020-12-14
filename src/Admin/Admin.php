<?php

namespace WcProToSL\Admin;

use WcProToSL\Admin\Woocommerce\CustomProductColumn;
use WcProToSL\Admin\Woocommerce\CustomProductField;
use WcProToSL\Admin\Woocommerce\PaymentComplete;

class Admin
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);

        new Options();
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
        wp_enqueue_style(
            'woocommerce-product-to-sendinblue-list',
            plugin_dir_url(__FILE__) . 'dist/admin/css/admin-app.css',
            [],
            WCPROTOSL_VERSION,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script(
            'woocommerce-product-to-sendinblue-list',
            plugin_dir_url(__FILE__) . 'dist/admin/js/admin-app.js',
            ['jquery'],
            WCPROTOSL_VERSION,
            false
        );
    }
}