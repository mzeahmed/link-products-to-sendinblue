<?php

namespace Wc_Sendinblue_Synchronize;

use Wc_Sendinblue_Synchronize\Admin\Admin;
use Wc_Sendinblue_Synchronize\Api\ApiClient;

/**
 * Class App
 *
 * @package Wc_Sendinblue_Synchronize
 */
class App
{
    public function __construct()
    {
        add_action('plugins_loaded', [$this, 'load_text_domain']);

        new ApiClient();
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
            WC_SS_TEXT_DOMAIN,
            false,
            WC_SS_PATH . '/langages'
        );
    }
}
