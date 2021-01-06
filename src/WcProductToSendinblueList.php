<?php

namespace WcProToSL;

use WcProToSL\Admin\WcProToSL_Settings;
use WcProToSL\Admin\Woocommerce\CustomProductColumn;
use WcProToSL\Admin\Woocommerce\CustomProductField;
use WcProToSL\Admin\Woocommerce\PaymentComplete;
use WcProToSL\Api\Api;

/**
 * @package WcProToSL
 * @since   1.0.8
 */
final class WcProductToSendinblueList
{
    /**
     * Instance of Wc_Product_To_Sendinblue_List
     *
     * @var WcProductToSendinblueList|null $instance create only one instance from plugin primary class
     */
    private static ?WcProductToSendinblueList $instance = null;

    public string $request_uri;
    public array $array;

    public function __construct()
    {
        $this->request_uri = $_SERVER['REQUEST_URI'];
        $this->array       = explode('/', $this->request_uri);

        add_action('plugins_loaded', [$this, 'init']);

        add_action('admin_enqueue_scripts', [$this, 'enqueueStyles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    /**
     * Getting instance This Class is a singleton class
     *
     * @return WcProductToSendinblueList
     * @since 1.0.8
     */
    public static function get_instance(): WcProductToSendinblueList
    {
        if (is_null((self::$instance))) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @since 1.0.8
     */
    public function init()
    {
        new Api();
        new WcProToSL_Settings();
        new CustomProductField();
        new PaymentComplete();
        new CustomProductColumn();

        add_action(
            'init',
            function () {
                load_plugin_textdomain(
                    WCPROTOSL_TEXT_DOMAIN,
                    false,
                    WCPROTOSL_PATH . 'langages'
                );
            }
        );
    }

    /**
     * @since 1.0.0
     */
    public function enqueueStyles()
    {
        if (str_contains($this->array[3], 'wc_product_to_sendinblue_list')) {
            wp_enqueue_style(
                'wcprotosl_bootstrap',
                WCPROTOSL_URL . 'assets/vendor/bootstrap/css/bootstrap.min.css',
                [],
                WCPROTOSL_VERSION,
                'all'
            );

            wp_enqueue_style(
                'wc_product_to_sendinblue_list',
                WCPROTOSL_URL . 'assets/css/app.css',
                [],
                WCPROTOSL_VERSION,
                'all'
            );
        }
    }

    /**
     * @since 1.0.0
     */
    public function enqueueScripts()
    {
        if (str_contains($this->array[3], 'wc_product_to_sendinblue_list')) {
            wp_enqueue_script(
                'wc_product_to_sendinblue_list',
                WCPROTOSL_URL . 'assets/js/app.js',
                ['jquery'],
                WCPROTOSL_VERSION,
                true
            );
        }
    }
}
