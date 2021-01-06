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
    const WCPROTOSL_API_KEY_GROUP = 'wc_product_to_sendinblue_list';

    public string $nonce_action;

    public function __construct()
    {
        $this->nonce_action = admin_url(
            'options-general.php?page=wc_product_to_sendinblue_list&tab=user_attributes'
        );

        add_action('admin_menu', [$this, 'addMenu']);
        add_action('admin_init', [$this, 'registerApiKeySettings']);
        add_action('admin_init', [$this, 'saveUserAttributes']);

        add_action('admin_notices', [$this, 'apiKeyNotice']);
        add_filter('plugin_action_links_' . WCPROTOSL_PLUGIN_BASENAME, [$this, 'pluginActionLinks']);

        add_action('admin_init', [$this, 'mainSettings']);
        add_action('admin_init', [$this, 'deleteApiKey']);

        //        delete_option(WCPROTOSL_CUSTOMER_ATTRIBUTES_OPTION);
        //        delete_option(WCPROTOSL_SENDINBLUE_ATTRIBUTES_OPTION);
        //        delete_transient('wcprotosl_attributes');
        //        delete_transient('wcprotosl_client_credit_' . md5(get_option(WCPROTOSL_API_KEY_V3_OPTION)));
    }

    /**
     * Add options menu
     *
     * @wp-hook admin_menu
     * @since   1.0.0
     */
    public function addMenu()
    {
        add_options_page(
            __('WC Product To Sendinblue list settings', WCPROTOSL_TEXT_DOMAIN),
            __('WC Product To Sendinblue List', WCPROTOSL_TEXT_DOMAIN),
            'manage_options',
            self::WCPROTOSL_API_KEY_GROUP,
            [$this, 'formRender']
        );
    }

    /**
     * Initialization of api key sections
     *
     * @wp-hook admin_init
     * @return void
     * @since   1.0.0
     */
    public function registerApiKeySettings()
    {
        register_setting(self::WCPROTOSL_API_KEY_GROUP, WCPROTOSL_API_KEY_V3_OPTION);

        add_settings_section(
            'api_key_section',
            __('API v3 Access key', WCPROTOSL_TEXT_DOMAIN),
            function () {
                printf(
                    __('<p><a href="%s" target="_blank">Get your account API key</a></p>', WCPROTOSL_TEXT_DOMAIN),
                    'https://account.sendinblue.com/advanced/api'
                );
            },
            self::WCPROTOSL_API_KEY_GROUP
        );

        add_settings_field(
            'api_key_field',
            __('API Key', WCPROTOSL_TEXT_DOMAIN),
            [$this, 'apiKeyFieldRender'],
            self::WCPROTOSL_API_KEY_GROUP,
            'api_key_section'
        );
    }

    /**
     * Save user attributes form datas
     *
     * @return void
     * @since 1.0.9
     */
    public function saveUserAttributes()
    {
        $customer_attributes = get_option(WCPROTOSL_CUSTOMER_ATTRIBUTES_OPTION);
        $contact_attributes  = get_option(WCPROTOSL_SENDINBLUE_ATTRIBUTES_OPTION);

        $customer_attributes == false ? add_option(WCPROTOSL_CUSTOMER_ATTRIBUTES_OPTION, []) : null;
        $contact_attributes == false ? add_option(WCPROTOSL_SENDINBLUE_ATTRIBUTES_OPTION, []) : null;

        if (isset($_POST['_user_attributes_nonce'])) {
            if ( ! wp_verify_nonce($_POST['_user_attributes_nonce'], $this->nonce_action)) {
                wp_die(
                    printf(
                        __('Sorry, nonce <strong>%s</strong> did not verify', WCPROTOSL_TEXT_DOMAIN),
                        '_user_attributes_nonce'
                    )
                );
            }

            if (isset($_POST['wcprotosl_woocommerce_customer_attributes'])) {
                update_option(
                    WCPROTOSL_CUSTOMER_ATTRIBUTES_OPTION,
                    $_POST['wcprotosl_woocommerce_customer_attributes']
                );
            }

            if (isset($_POST['wcprotosl_sendinblue_contact_attributes'])) {
                update_option(
                    WCPROTOSL_SENDINBLUE_ATTRIBUTES_OPTION,
                    $_POST['wcprotosl_sendinblue_contact_attributes']
                );
            }

            wp_safe_redirect(wp_get_referer());
            exit();
        }
    }

    /**
     * Display api key fields
     *
     * @return string
     * @since 1.0.0
     */
    public function apiKeyFieldRender(): string
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
    public function formRender(): string
    {
        $api_key                      = get_option(WCPROTOSL_API_KEY_V3_OPTION);
        $customer_attributes_option   = get_option(WCPROTOSL_CUSTOMER_ATTRIBUTES_OPTION);
        $sendinblue_attributes_option = get_option(WCPROTOSL_SENDINBLUE_ATTRIBUTES_OPTION);

        $admin_profile   = new WC_Admin_Profile();
        $customer_fields = $admin_profile->get_customer_meta_fields();
        $allAttrs        = ApiManager::get_attributes();

        $contact_attributes = $allAttrs['attributes']['normal_attributes'];

        return View::render(
            'admin/options/form',
            [
                'api_field_group'              => self::WCPROTOSL_API_KEY_GROUP,
                'api_key'                      => $api_key,
                'customer_attributes_option'   => $customer_attributes_option,
                'sendinblue_attributes_option' => $sendinblue_attributes_option,
                'customer_fields'              => $customer_fields,
                'contact_attributes'           => $contact_attributes,
                'nonce_action'                 => $this->nonce_action,
                'matched_attributes'           => $this->getMatchedAttributes()
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
    public function apiKeyNotice(): string
    {
        if (empty(get_option(WCPROTOSL_API_KEY_V3_OPTION))
            || get_option(WCPROTOSL_API_KEY_V3_OPTION) == false) {
            return View::render('admin/options/partials/notice', []);
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
    public function pluginActionLinks($links)
    {
        $settings_links = [
            '<a href="' . admin_url('options-general.php?page=wc_product_to_sendinblue_list') . '">' .
            esc_html__('Settings', WCPROTOSL_TEXT_DOMAIN) . '</a>',
        ];

        return array_merge($settings_links, $links);
    }

    /**
     * Register main settings option
     *
     * @return void
     * @since 1.0.6
     */
    public function mainSettings()
    {
        get_option(WCPROTOSL_MAIN_OPTION) == false ? add_option(WCPROTOSL_MAIN_OPTION, []) : null;

        if ( ! empty(get_option(WCPROTOSL_API_KEY_V3_OPTION))) {
            $accoun_info = ApiManager::get_account_info();

            $args = [
                'account_email'             => $accoun_info['account_email'],
                'access_key'                => get_option(WCPROTOSL_API_KEY_V3_OPTION),
                'client_matched_attributes' => array_combine(
                    get_option(WCPROTOSL_SENDINBLUE_ATTRIBUTES_OPTION),
                    get_option(WCPROTOSL_CUSTOMER_ATTRIBUTES_OPTION)
                )
            ];

            update_option(WCPROTOSL_MAIN_OPTION, $args);
        } else {
            update_option(WCPROTOSL_MAIN_OPTION, []);
        }
    }

    /**
     * Delete API KEY
     *
     * @return void
     * @since 1.0.6
     */
    public function deleteApiKey()
    {
        if (isset($_POST['wcprotosl_delete_api_key'])) {
            delete_option(WCPROTOSL_API_KEY_V3_OPTION);

            wp_safe_redirect(wp_get_referer());
            exit();
        }
    }

    /**
     * @return mixed
     * @since 1.0.7
     */
    private function getMatchedAttributes()
    {
        return array_combine(
            get_option(WCPROTOSL_CUSTOMER_ATTRIBUTES_OPTION),
            get_option(WCPROTOSL_SENDINBLUE_ATTRIBUTES_OPTION)
        );
    }
}
