<?php

namespace WcProToSL\Admin;

use WC_Admin_Profile;
use WcProToSL\Api\ApiManager;
use WcProToSL\Renderer\Renderer;

/**
 * Class Options
 *
 * @package WcProToSL\Admin
 */
class Options
{
    // api key option
    const WCPROTOSL_GROUP = 'woocommerce_product_to_sendinblue_list';
    const WCPROTOSL_API_KEY_V3_OPTION_NAME = 'wcprotosl_api_key';

    // attributes synch option
    const WCPROTOSL_ATTRIBUTES_SYNCH_GROUP = "wcprotosl_attributes_synch_group";
    const WCPROTOSL_ATTRIBUTES_SYNCH_OPTION_NAME = 'wcprotosl_attributes_synch';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);

        register_activation_hook(__FILE__, [$this, 'activation']);
        add_action('admin_init', [$this, 'redirect']);

        add_action('admin_notices', [$this, 'notice']);

        add_filter('plugin_action_links_' . WCPROTOSL_PLUGIN_BASENAME, [$this, 'addPluginSettingsLink']);
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
            __('WC Product To Sendinblue list settings', WCPROTOSL_TEXT_DOMAIN),
            __('WC Product To Sendinblue List', WCPROTOSL_TEXT_DOMAIN),
            'manage_options',
            self::WCPROTOSL_GROUP,
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
        register_setting(self::WCPROTOSL_GROUP, self::WCPROTOSL_API_KEY_V3_OPTION_NAME);
        register_setting(self::WCPROTOSL_ATTRIBUTES_SYNCH_GROUP, self::WCPROTOSL_ATTRIBUTES_SYNCH_OPTION_NAME);

        add_settings_section(
            'wcprotosl_options_section',
            __('API v3 Access key', WCPROTOSL_TEXT_DOMAIN),
            function () {
                printf(
                    __('<p><a href="%s">Get your account API key</a></p>', WCPROTOSL_TEXT_DOMAIN),
                    'https://account.sendinblue.com/advanced/api'
                );
            },
            self::WCPROTOSL_GROUP
        );

        add_settings_field(
            'wcprotosl_api_key',
            __('API Key', WCPROTOSL_TEXT_DOMAIN),
            [$this, 'api_key_field_render'],
            self::WCPROTOSL_GROUP,
            'wcprotosl_options_section'
        );

        add_settings_section(
            'wcprotosl_attributes_synch_section',
            __('User attributes synch', WCPROTOSL_TEXT_DOMAIN),
            [$this, 'attributes_synch_render'],
            self::WCPROTOSL_ATTRIBUTES_SYNCH_GROUP
        );

        add_settings_field(
            'wcprotosl_attributes_synch_field',
            __('Attributes', WCPROTOSL_TEXT_DOMAIN),
            '',
            self::WCPROTOSL_ATTRIBUTES_SYNCH_GROUP
        );
    }

    /**
     * Display form field partial
     *
     * @return string
     * @since 1.0.0
     */
    public function api_key_field_render(): string
    {
        return Renderer::render(
            'admin/options/partials/api-key-field',
            [
                'api_key_v3' => get_option(self::WCPROTOSL_API_KEY_V3_OPTION_NAME),
            ]
        );
    }

    /**
     * Display api key section
     *
     * @return string | void
     * @since 1.0.0
     */
    public function form_render(): string
    {
        $options = get_option(self::WCPROTOSL_API_KEY_V3_OPTION_NAME);

        return Renderer::render(
            'admin/options/form',
            [
                'api_field_group'        => self::WCPROTOSL_GROUP,
                'attributes_synch_group' => self::WCPROTOSL_ATTRIBUTES_SYNCH_GROUP,
                'options'                => $options,
            ]
        );
    }

    /**
     * Display attributes synch section
     *
     * @return string
     * @since 1.0.5
     */
    public function attributes_synch_render(): string
    {
        // get woocommerce customer's attributes
        $admin_profile   = new WC_Admin_Profile();
        $customer_fields = $admin_profile->get_customer_meta_fields();

        // available sendinblue attributes
        $allAttrs = ApiManager::get_attributes();
        $attrs    = $allAttrs['attributes']['normal_attributes'];

        return Renderer::render(
            'admin/options/attributes-synch',
            [
                'customer_fields' => $customer_fields,
                'attrs'           => $attrs,
            ]
        );
    }

    /**
     * Add option on plugin activation
     */
    public function activation()
    {
        add_option('wcprotosl_do_activation_redirect', true);
    }

    /**
     * Redirect after activate plugin
     *
     * @wp-hook admin_init
     * @return void
     * @since   1.0.0
     */
    public function redirect()
    {
        if (get_option('wcprotosl_do_activation_redirect', false)) {
            delete_option('wcprotosl_do_activation_redirect');
            if (isset($_GET['activate-multi'])) {
                wp_safe_redirect(admin_url('options-general.php?page=woocommerce_product_to_sendinblue_list'));
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
        $option = get_option(self::WCPROTOSL_API_KEY_V3_OPTION_NAME);

        if ( ! $option) {
            return Renderer::render('admin/options/partials/notice');
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
        $links[] = '<a href="' . admin_url('options-general.php?page=woocommerce_product_to_sendinblue_list') . '">' .
                   __('Settings', WCPROTOSL_TEXT_DOMAIN) . '</a>';

        return $links;
    }
}