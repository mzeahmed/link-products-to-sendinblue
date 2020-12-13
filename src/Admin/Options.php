<?php

namespace Wc_Sendinblue_Synchronize\Admin;

use Wc_Sendinblue_Synchronize\Renderer\Renderer;

/**
 * Class Options
 *
 * @package Wc_Sendinblue_Synchronize\Admin
 */
class Options
{
    const GROUP = 'wc_sendinblue_synchronize';
    const WC_SS_API_KEY_V3_OPTION_NAME = 'wc_sendinblue_synchronize_apiKey';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);

        register_activation_hook(__FILE__, [$this, 'wc_ss_activation']);
        add_action('admin_init', [$this, 'wc_ss_redirect']);

        add_action('admin_notices', [$this, 'notice']);

        add_filter('plugin_action_links_' . WC_SS_PLUGIN_BASENAME, [$this, 'addPluginSettingsLink']);

//        add_action('admin_init', [$this, 'wc_ss_main_option']);
    }

    public function wc_ss_main_option()
    {
        $option_values = [
            'access_key'    => get_option(self::WC_SS_API_KEY_V3_OPTION_NAME),
            'account_email' => 'test',
        ];

        if ( ! get_option(self::WC_SS_API_KEY_V3_OPTION_NAME)) {
            return;
        }

        if (get_option('wc_ss_main_option')) {
            update_option('wc_ss_main_option', 'test1');
        } else {
            add_option('wc_ss_main_option', 'test2');
        }
    }

    /**
     * Add options menu
     *
     * @wp-hook admin_menu
     * @since   1.0.0
     */
    public function add_menu()
    {
        add_options_page(
            __('WC Sendinblue Synchronize settings', WC_SS_TEXT_DOMAIN),
            __('WC Sendinblue Synchronize', WC_SS_TEXT_DOMAIN),
            'manage_options',
            self::GROUP,
            [$this, 'form_render']
        );
    }

    /**
     * Initialization of sections and params fields
     *
     * @wp-hook admin_init
     * @return void
     * @since   1.0.0
     */
    public function register_settings()
    {
        register_setting(self::GROUP, self::WC_SS_API_KEY_V3_OPTION_NAME);

        add_settings_section(
            'wc_sendinblue_synchronize_options_section',
            __('Enter your API v3 Access key', WC_SS_TEXT_DOMAIN),
            function () {
                printf(
                    __('<p><a href="%s">Get your account API key</a></p>', WC_SS_TEXT_DOMAIN),
                    'https://account.sendinblue.com/advanced/api'
                );
            },
            self::GROUP
        );

        add_settings_field(
            'wc_sendinblue_synchronize_api_key',
            __('API Key', WC_SS_TEXT_DOMAIN),
            [$this, 'settings_field'],
            self::GROUP,
            'wc_sendinblue_synchronize_options_section'
        );
    }

    /**
     * Display form field
     *
     * @return string
     * @since 1.0.0
     */
    public function settings_field(): string
    {
        return Renderer::render(
            'admin/options/settings_field',
            [
                'api_key_v3' => get_option(self::WC_SS_API_KEY_V3_OPTION_NAME),
            ]
        );
    }

    /**
     * Display api key form
     *
     * @return string | void
     * @since 1.0.0
     */
    public function form_render(): string
    {
        return Renderer::render(
            'admin/options/apikey_form',
            [
                'group' => self::GROUP,
            ]
        );

    }

    /**
     * Add option on plugin activation
     */
    public function wc_ss_activation()
    {
        add_option('wc_sendinblue_synchronize_do_activation_redirect', true);
    }

    /**
     * Redirect after activate plugin
     *
     * @wp-hook admin_init
     * @return void
     * @since   1.0.0
     */
    public function wc_ss_redirect()
    {
        if (get_option('wc_sendinblue_synchronize_do_activation_redirect', false)) {
            delete_option('wc_sendinblue_synchronize_do_activation_redirect');
            if (isset($_GET['activate-multi'])) {
                wp_safe_redirect(admin_url('options-general.php?page=wc_sendinblue_synchronize'));
                exit();
            }
        }
    }

    /**
     * Notice if the sendinblue API key is empty
     *
     * @wp-hook admin_notices
     * @return string | void
     * @since   1.0.0
     */
    public function notice(): string
    {
        $option = get_option(self::WC_SS_API_KEY_V3_OPTION_NAME);

        if ( ! $option) {
            return Renderer::render('admin/options/notice');
        }

        return false;
    }

    /**
     * Add plugin settings link under plugin name on plugins page
     *
     * @param $links
     *
     * @wp-hook plugin_action_links_ . __FILE__
     * @return mixed
     * @since   1.0.0
     */
    public function addPluginSettingsLink($links)
    {
        $links[] = '<a href="' . admin_url('options-general.php?page=wc_sendinblue_synchronize') . '">' .
                   __('Settings', WC_SS_TEXT_DOMAIN) . '</a>';

        return $links;
    }
}