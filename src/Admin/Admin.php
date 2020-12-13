<?php

namespace Wc_Sendinblue_Synchronize\Admin;

use Wc_Sendinblue_Synchronize\Admin\Woocommerce\CustomProductColumn;
use Wc_Sendinblue_Synchronize\Admin\Woocommerce\CustomProductField;
use Wc_Sendinblue_Synchronize\Admin\Woocommerce\PaymentComplete;

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
            'wc-sendinblue-synchronize',
            plugin_dir_url(__FILE__) . 'dist/admin/css/admin-app.css',
            [],
            WC_SS_VERSION,
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
            'wc-sendinblue-synchronize',
            plugin_dir_url(__FILE__) . 'dist/admin/js/admin-app.js',
            ['jquery'],
            WC_SS_VERSION,
            false
        );
    }
}