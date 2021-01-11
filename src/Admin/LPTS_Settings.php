<?php

namespace LPTS\Admin;

use WC_Admin_Profile;
use LPTS\Api\ApiManager;
use LPTS\View\View;

/**
 * Class Options
 *
 * @package LPTS\Admin
 * @since   1.0.0
 */
class LPTS_Settings
{
    const LPTS_API_KEY_GROUP = 'link_products_to_sendinblue';

    public string $nonce_action;

    public function __construct()
    {
        $this->nonce_action = admin_url(
            'options-general.php?page=link_products_to_sendinblue&tab=user_attributes'
        );

        add_action('admin_menu', [$this, 'addMenu']);
        add_action('admin_init', [$this, 'registerApiKeySettings']);
        add_action('admin_init', [$this, 'saveUserAttributes']);

        add_action('admin_notices', [$this, 'apiKeyNotice']);
        add_filter('plugin_action_links_' . LPTS_PLUGIN_BASENAME, [$this, 'pluginActionLinks']);

        add_action('admin_init', [$this, 'mainSettings']);
        add_action('admin_init', [$this, 'deleteApiKey']);
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
            __('Link Products To Sendinblue Lists From WooCommerce settings', LPTS_TEXT_DOMAIN),
            __('Link Products To Sendinblue Lists From WooCommerce', LPTS_TEXT_DOMAIN),
            'manage_options',
            self::LPTS_API_KEY_GROUP,
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
        register_setting(self::LPTS_API_KEY_GROUP, LPTS_API_KEY_V3_OPTION);

        add_settings_section(
            'api_key_section',
            __('API v3 Access key', LPTS_TEXT_DOMAIN),
            function () {
                printf(
                    __('<p><a href="%s" target="_blank">Get your account API key</a></p>', LPTS_TEXT_DOMAIN),
                    'https://account.sendinblue.com/advanced/api'
                );
            },
            self::LPTS_API_KEY_GROUP
        );

        add_settings_field(
            'api_key_field',
            __('API Key', LPTS_TEXT_DOMAIN),
            [$this, 'apiKeyFieldRender'],
            self::LPTS_API_KEY_GROUP,
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
        /**
         * Options
         */
        $customer_attributes = get_option(LPTS_CUSTOMER_ATTRIBUTES_OPTION);
        $contact_attributes  = get_option(LPTS_SENDINBLUE_ATTRIBUTES_OPTION);

        /**
         * Add options if they don't exists
         */
        $customer_attributes == false ? add_option(LPTS_CUSTOMER_ATTRIBUTES_OPTION, []) : null;
        $contact_attributes == false ? add_option(LPTS_SENDINBLUE_ATTRIBUTES_OPTION, []) : null;

        if (isset($_POST['_user_attributes_nonce'])) {
            if ( ! wp_verify_nonce($_POST['_user_attributes_nonce'], $this->nonce_action)) {
                wp_die(
                    printf(
                        esc_html__('Sorry, nonce <strong>%s</strong> did not verify', LPTS_TEXT_DOMAIN),
                        '_user_attributes_nonce'
                    )
                );
            }

            if (isset($_POST['lpts_woocommerce_customer_attributes'])) {
                update_option(
                    LPTS_CUSTOMER_ATTRIBUTES_OPTION,
                    $this->sanitize_user_attributes_form_fields($_POST['lpts_woocommerce_customer_attributes'])
                );
            }

            if (isset($_POST['lpts_sendinblue_contact_attributes'])) {
                update_option(
                    LPTS_SENDINBLUE_ATTRIBUTES_OPTION,
                    $this->sanitize_user_attributes_form_fields($_POST['lpts_sendinblue_contact_attributes'])
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
                'api_key_v3' => get_option(LPTS_API_KEY_V3_OPTION),
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
        $api_key                      = get_option(LPTS_API_KEY_V3_OPTION);
        $customer_attributes_option   = get_option(LPTS_CUSTOMER_ATTRIBUTES_OPTION);
        $sendinblue_attributes_option = get_option(LPTS_SENDINBLUE_ATTRIBUTES_OPTION);

        $admin_profile   = new WC_Admin_Profile();
        $customer_fields = $admin_profile->get_customer_meta_fields();
        $allAttrs        = ApiManager::get_attributes();

        $contact_attributes = $allAttrs['attributes']['normal_attributes'];

        return View::render(
            'admin/options/form',
            [
                'api_field_group'              => self::LPTS_API_KEY_GROUP,
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
        if (empty(get_option(LPTS_API_KEY_V3_OPTION)) || get_option(LPTS_API_KEY_V3_OPTION) == false) {
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
            '<a href="' . admin_url('options-general.php?page=link_products_to_sendinblue') . '">' .
            esc_html__('Settings', LPTS_TEXT_DOMAIN) . '</a>',
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
        get_option(LPTS_MAIN_OPTION) == false ? add_option(LPTS_MAIN_OPTION, []) : null;

        if ( ! empty(get_option(LPTS_API_KEY_V3_OPTION))) {
            $accoun_info = ApiManager::get_account_info();

            $args = [
                'account_email'             => $accoun_info['account_email'],
                'access_key'                => get_option(LPTS_API_KEY_V3_OPTION),
                'client_matched_attributes' => array_combine(
                    get_option(LPTS_SENDINBLUE_ATTRIBUTES_OPTION),
                    get_option(LPTS_CUSTOMER_ATTRIBUTES_OPTION)
                )
            ];

            update_option(LPTS_MAIN_OPTION, $args);
        } else {
            update_option(LPTS_MAIN_OPTION, []);
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
        if (isset($_POST['lpts_delete_api_key'])) {
            delete_option(LPTS_API_KEY_V3_OPTION);

            wp_safe_redirect(wp_get_referer());
            exit();
        }
    }

    /**
     * Combine options LPTS_CUSTOMER_ATTRIBUTES_OPTION and LPTS_SENDINBLUE_ATTRIBUTES_OPTION and return result in array
     *
     * @return mixed
     * @since 1.0.7
     */
    private function getMatchedAttributes()
    {
        return array_combine(
            get_option(LPTS_CUSTOMER_ATTRIBUTES_OPTION),
            get_option(LPTS_SENDINBLUE_ATTRIBUTES_OPTION)
        );
    }

    /**
     * Sanitize datas of user attributes synch form
     *
     * @param $datas
     *
     * @return array
     * @since 1.1.5
     */
    private function sanitize_user_attributes_form_fields($datas): array
    {
        $field = [];

        foreach ($datas as $k => $v) {
            $field[$k] = sanitize_text_field($v);
        }

        return $field;
    }
}
