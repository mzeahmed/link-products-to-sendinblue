<?php

namespace WcProToSL;

use WcProToSL\Admin\Admin;
use WcProToSL\Admin\WcProToSl_Settings;
use WcProToSL\Api\Api;

/**
 * Class App
 *
 * @package WcProToSL
 */
class App
{
    public function __construct()
    {
        add_action('plugins_loaded', [$this, 'load_text_domain']);

        new Api();
        new Admin();
    }

    /**
     * Textdomain load
     *
     * @return void
     * @since 1.0.0
     */
    public function load_text_domain()
    {
        load_plugin_textdomain(
            WCPROTOSL_TEXT_DOMAIN,
            false,
            WCPROTOSL_PATH . '/langages'
        );
    }
}
