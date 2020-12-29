<?php

namespace WcProToSL;

/**
 * @package WcProToSL
 * @since 1.0.8
 */
final class WoocommerceProductToSendinblueList
{
    /**
     * Instance property of Woocommerce_Product_To_Sendinblue_List Class.
     *
     * @var WoocommerceProductToSendinblueList|null $instance create only one instance from plugin primary class
     * @static
     */
    private static ?WoocommerceProductToSendinblueList $instance = null;

    public function __construct()
    {
        add_action('plugins_loaded', [$this, 'init']);
    }

    /**
     * Create an instance from Woocommerce_Product_To_Sendinblue_List class.
     *
     * @return WoocommerceProductToSendinblueList
     * @since 1.0.8
     */
    public static function get_instance(): WoocommerceProductToSendinblueList
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
        new \WcProToSL\Api\Api();
        new \WcProToSL\Admin\Admin();

        add_action('init', function () {
            load_plugin_textdomain(
                WCPROTOSL_TEXT_DOMAIN,
                false,
                WCPROTOSL_PATH . '/langages'
            );
        });
    }
}
