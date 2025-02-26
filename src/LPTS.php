<?php

declare(strict_types=1);

namespace LPTS;

use LPTS\Admin\LPTS_Settings;
use LPTS\Admin\Woocommerce\PaymentComplete;
use LPTS\Admin\Woocommerce\CustomProductField;
use LPTS\Admin\Woocommerce\CustomProductColumn;

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
        add_action('admin_enqueue_scripts', [$this, 'registerScrpts']);
    }

    /**
     * Getting instance This Class is a singleton class
     *
     * @return LPTS|null
     * @since 1.0.0
     */
    public static function getInstance(): ?LPTS
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Loads the necessary classes of the plugin
     *
     * @since 1.0.0
     */
    public function load(): void
    {
        new LPTS_Settings();
        new CustomProductField();
        new PaymentComplete();
        new CustomProductColumn();

        $this->loadTextDomain();
    }

    /**
     * Register plugin scripts and styles
     *
     * @return void
     * @since 1.1.5
     */
    public function registerScrpts(): void
    {
        if (isset($_GET['page']) && 'link_products_to_sendinblue' === $_GET['page']) {
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

            if (isset($_GET['tab']) && 'user_attributes' === $_GET['tab']) {
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

    /**
     * Load the plugin text domain for translation
     *
     * @return void
     * @since 1.1.7.4
     */
    private function loadTextDomain(): void
    {
        load_plugin_textdomain(
            'link-products-to-sendinblue',
            false,
            LPTS_PATH . 'languages'
        );
    }
}
