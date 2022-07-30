<?php

namespace LPTS;

use LPTS\Admin\LPTS_Settings;
use LPTS\Admin\Woocommerce\CustomProductColumn;
use LPTS\Admin\Woocommerce\CustomProductField;
use LPTS\Admin\Woocommerce\PaymentComplete;

/**
 * @package LPTS
 * @since   1.0.0
 */
final class LPTS
{
    /**
     * Instance of LPTS
     *
     * @var LPTS|null $instance create only one instance from plugin primary class
     */
    private static ?LPTS $instance = null;
    public string $request_uri;
    public array $array;

    public function __construct()
    {
        add_action('init', [$this, 'load']);

        add_action('admin_enqueue_scripts', [$this, 'enqueueStyles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    /**
     * Getting instance This Class is a singleton class
     *
     * @return LPTS|null
     * @since 1.0.0
     */
    public static function getInstance() : ? LPTS
    {
        if (is_null((self::$instance))) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Loads the necessary classes of the plugin
     *
     * @since 1.0.0
     */
    public function load()
    {
        new LPTS_Settings();
        new CustomProductField();
        new PaymentComplete();
        new CustomProductColumn();

        add_action('init', function () {
            load_plugin_textdomain(
                LPTS_TEXT_DOMAIN,
                false,
                LPTS_PATH . 'languages'
            );
        });
    }

    /**
     * Load plugin stylesheet
     *
     * @since 1.0.0
     */
    public function enqueueStyles()
    {
        // Load CSS on all plugin options pages
        if (isset($_GET['page']) && $_GET['page'] === 'link_products_to_sendinblue') {
            wp_enqueue_style(
                'lpts_bootstrap',
                LPTS_URL . 'assets/vendor/bootstrap/css/bootstrap.min.css',
                [],
                LPTS_VERSION,
                'all'
            );

            wp_enqueue_style(
                'link_products_to_sendinblue',
                LPTS_URL . 'assets/css/app.css',
                [],
                LPTS_VERSION,
                'all'
            );
        }
    }

    /**
     * Load plugin script
     *
     * @since 1.0.0
     */
    public function enqueueScripts()
    {
        // Load JS only on the plugin options "User attributes sync" tab
        if (isset($_GET['page']) && $_GET['page'] === 'link_products_to_sendinblue' && isset($_GET['tab']) && $_GET['tab'] === 'user_attributes') {
            wp_enqueue_script(
                'link_products_to_sendinblue',
                LPTS_URL . 'assets/js/app.js',
                ['jquery'],
                LPTS_VERSION,
                true
            );
        }
    }
}
