<?php

namespace WcProToSL\Admin;

use WC_Admin_Profile;
use WcProToSL\Api\ApiManager;
use WcProToSL\View\View;

/**
 * Class Options
 *
 * @package WcProToSL\Admin
 */
class WcProToSL_Settings
{
    const WCPROTOSL_API_KEY_GROUP = 'woocommerce_product_to_sendinblue_list';

    const WCPROTOSL_ATTRIBUTES_SYNCH_GROUP = "wcprotosl_attributes_synch_group";

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_api_key_settings']);
        add_action('admin_init', [$this, 'register_user_attributes_settings']);

        add_action('admin_notices', [$this, 'api_key_notice']);
        add_filter('plugin_action_links', [$this, 'plugin_action_links'], WCPROTOSL_PLUGIN_BASENAME);

        add_action('admin_init', [$this, 'main_settings']);
        add_action('admin_init', [$this, 'delete_api_key']);
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
            self::WCPROTOSL_API_KEY_GROUP,
            [$this, 'form_render']
        );
    }

    /**
     * Initialization of api key sections
     *
     * @wp-hook admin_init
     * @return void
     * @since   1.0.0
     */
    public function register_api_key_settings()
    {
        register_setting(self::WCPROTOSL_API_KEY_GROUP, WCPROTOSL_API_KEY_V3_OPTION);

        add_settings_section(
            'api_key_section',
            __('API v3 Access key', WCPROTOSL_TEXT_DOMAIN),
            function () {
                printf(
                    __('<p><a href="%s">Get your account API key</a></p>', WCPROTOSL_TEXT_DOMAIN),
                    'https://account.sendinblue.com/advanced/api'
                );
            },
            self::WCPROTOSL_API_KEY_GROUP
        );

        add_settings_field(
            'api_key_field',
            __('API Key', WCPROTOSL_TEXT_DOMAIN),
            [$this, 'api_key_field_render'],
            self::WCPROTOSL_API_KEY_GROUP,
            'api_key_section'
        );
    }

    /**
     * Initialization of user attributes synch section
     *
     * @wp-hook admin_init
     * @return void
     * @since   1.0.7
     */
    public function register_user_attributes_settings()
    {
        register_setting(
            self::WCPROTOSL_ATTRIBUTES_SYNCH_GROUP,
            WCPROTOSL_CUSTOMER_ATTRIBUTES_OPTION,
            [
                'type' => 'array',
            ]
        );
        register_setting(
            self::WCPROTOSL_ATTRIBUTES_SYNCH_GROUP,
            WCPROTOSL_SENDINBLUE_ATTRIBUTES_OPTION,
            [
                'type' => 'array',
            ]
        );

        add_settings_section(
            'user_attributes_synch_section',
            __('User attributes synch', WCPROTOSL_TEXT_DOMAIN),
            '',
            self::WCPROTOSL_ATTRIBUTES_SYNCH_GROUP
        );

        add_settings_field(
            'wcprotosl_woocommerce_customer_attributes',
            '',
            [$this, 'woocommerce_customer_attributes_field_render'],
            self::WCPROTOSL_ATTRIBUTES_SYNCH_GROUP,
            'user_attributes_synch_section'
        );

        add_settings_field(
            'wcprotosl_sendinblue_contact_attributes',
            '',
            [$this, 'sendinblue_contact_attributes_field_render'],
            self::WCPROTOSL_ATTRIBUTES_SYNCH_GROUP,
            'user_attributes_synch_section'
        );
    }

    /**
     * Display api key fields
     *
     * @return string
     * @since 1.0.0
     */
    public function api_key_field_render(): string
    {
        return View::render(
            'admin/options/partials/api-key-field',
            [
                'api_key_v3' => get_option(WCPROTOSL_API_KEY_V3_OPTION),
            ]
        );
    }

    /**
     * Display form
     *
     * @return string | void
     * @since 1.0.0
     */
    public function form_render(): string
    {
        $api_key = get_option(WCPROTOSL_API_KEY_V3_OPTION);
        $customer_attributes = get_option(WCPROTOSL_CUSTOMER_ATTRIBUTES_OPTION);
        $sendinblue_attributes = get_option(WCPROTOSL_SENDINBLUE_ATTRIBUTES_OPTION);

        return View::render(
            'admin/options/form',
            [
                'api_field_group' => self::WCPROTOSL_API_KEY_GROUP,
                'attributes_synch_group' => self::WCPROTOSL_ATTRIBUTES_SYNCH_GROUP,
                'api_key' => $api_key,
                'customer_attributes' => $customer_attributes,
                'sendinblue_attributes' => $sendinblue_attributes
            ]
        );
    }

    /**
     * Display woocommerce attributes fields
     *
     * @return string
     * @since 1.0.7
     */
    public function woocommerce_customer_attributes_field_render(): string
    {
        $admin_profile = new WC_Admin_Profile();
        $customer_fields = $admin_profile->get_customer_meta_fields();

        return View::render(
            'admin/options/partials/woocommerce-customer-attributes-field',
            [
                'customer_fields' => $customer_fields,
                'option_values' => get_option(WCPROTOSL_CUSTOMER_ATTRIBUTES_OPTION)
            ]
        );
    }

    /**
     * Display Sendinblue contact attributes
     *
     * @return string
     * @since 1.0.7
     */
    public function sendinblue_contact_attributes_field_render(): string
    {
        $allAttrs = ApiManager::get_attributes();
        $contact_attributes = $allAttrs['attributes']['normal_attributes'];

        return View::render(
            'admin/options/partials/sendinblue-contact-attributes-fields',
            [
                'contact_attributes' => $contact_attributes,
                'option_values' => get_option(WCPROTOSL_SENDINBLUE_ATTRIBUTES_OPTION)

            ]
        );
    }

    /**
     * Notice if the sendinblue API key is empty
     *
     * @wp-hook admin_notices
     * @return string | void
     * @since   1.0.0
     */
    public function api_key_notice(): string
    {
        if (empty(get_option(WCPROTOSL_API_KEY_V3_OPTION))
            || get_option(WCPROTOSL_API_KEY_V3_OPTION) == false) {
            return View::render('admin/options/partials/notice');
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
    public function plugin_action_links($links)
    {
        $settings_links = [
            '<a href="' . admin_url('options-general.php?page=woocommerce_product_to_sendinblue_list') . '">' .
            esc_html__('Settings', WCPROTOSL_TEXT_DOMAIN) . '</a>'
        ];

        return array_merge($settings_links, $links);
    }

    /**
     * Add main settings | $account_email $access_key
     *
     * @return void
     * @since 1.0.6
     */
    public function main_settings()
    {
        get_option(WCPROTOSL_MAIN_OPTION_NAME) == false ? add_option(WCPROTOSL_MAIN_OPTION_NAME, []) : null;

        if (get_option(WCPROTOSL_API_KEY_V3_OPTION)) {
            $accoun_info = ApiManager::get_account_info();

            $args = [
                'account_email' => $accoun_info['account_email'],
                'access_key' => get_option(WCPROTOSL_API_KEY_V3_OPTION),
            ];

            update_option(WCPROTOSL_MAIN_OPTION_NAME, $args);
        } else {
            update_option(WCPROTOSL_MAIN_OPTION_NAME, []);
        }
    }

    /**
     * Delete API KEY
     *
     * @return void
     * @since 1.0.6
     */
    public function delete_api_key()
    {
        if (isset($_POST['wcprotosl_delete_api_key'])) {
            delete_option(WCPROTOSL_API_KEY_V3_OPTION);

            wp_safe_redirect(wp_get_referer());
            exit();
        }
    }

    /**
     * Combine self::WCPROTOSL_CUSTOMER_ATTRIBUTES_OPTION and self::WCPROTOSL_SENDINBLUE_ATTRIBUTES_OPTION and return result
     *
     * @return array
     * @since 1.0.7
     */
    public function get_matched_attributes(): array
    {
        $customer_attributes = get_option(WCPROTOSL_CUSTOMER_ATTRIBUTES_OPTION);
        $contact_attributes = get_option(WCPROTOSL_SENDINBLUE_ATTRIBUTES_OPTION);

        return array_combine($customer_attributes, $contact_attributes);
    }
}
