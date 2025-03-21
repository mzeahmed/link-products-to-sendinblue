<?php

declare(strict_types=1);

namespace LPTS\Infrastructure\WordPress\Hook\Admin;

use LPTS\Constants;
use LPTS\Infrastructure\View\View;
use LPTS\Infrastructure\View\Renderer;
use LPTS\Infrastructure\External\Brevo\ApiManager;

class SettingsHook
{
    public const LPTS_API_KEY_GROUP = 'link_products_to_sendinblue';

    public ?string $nonce_action;

    public function __construct(
        private Renderer $renderer
    ) {
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
    public function addMenu(): void
    {
        add_options_page(
            __('Link Products To Sendinblue Lists From WooCommerce settings', 'link-products-to-sendinblue'),
            __('Link Products To Sendinblue Lists From WooCommerce', 'link-products-to-sendinblue'),
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
    public function registerApiKeySettings(): void
    {
        register_setting(self::LPTS_API_KEY_GROUP, Constants::LPTS_API_KEY_V3_OPTION);

        add_settings_section(
            'api_key_section',
            __('API v3 Access key', 'link-products-to-sendinblue'),
            static function () {
                printf(
                    __(
                        '<p><a href="%s" target="_blank">Get your account API key</a></p>',
                        'link-products-to-sendinblue'
                    ),
                    'https://app.brevo.com/settings/keys/api'
                );
            },
            self::LPTS_API_KEY_GROUP
        );

        add_settings_field(
            'api_key_field',
            __('API Key', 'link-products-to-sendinblue'),
            [$this, 'apiKeyFieldRender'],
            self::LPTS_API_KEY_GROUP,
            'api_key_section'
        );
    }

    /**
     * Save user attributes form datas
     *
     * @return void
     * @since 1.0.0
     */
    public function saveUserAttributes(): void
    {
        /**
         * Options
         */
        $customer_attributes = get_option(Constants::LPTS_CUSTOMER_ATTRIBUTES_OPTION);
        $contact_attributes = get_option(Constants::LPTS_SENDINBLUE_ATTRIBUTES_OPTION);

        /**
         * Add options if they don't exists
         */
        false === $customer_attributes ? add_option(Constants::LPTS_CUSTOMER_ATTRIBUTES_OPTION, []) : null;
        false === $contact_attributes ? add_option(Constants::LPTS_SENDINBLUE_ATTRIBUTES_OPTION, []) : null;

        if (isset($_POST['_user_attributes_nonce'])) {
            if (!wp_verify_nonce($_POST['_user_attributes_nonce'], $this->nonce_action)) {
                wp_die(
                    printf(
                        esc_html__(
                            'Sorry, nonce <strong>%s</strong> did not verify',
                            'link-products-to-sendinblue'
                        ),
                        '_user_attributes_nonce'
                    )
                );
            }

            if (isset($_POST['lpts_woocommerce_customer_attributes'])) {
                update_option(
                    Constants::LPTS_CUSTOMER_ATTRIBUTES_OPTION,
                    $this->sanitizeUserAttributesFormFields($_POST['lpts_woocommerce_customer_attributes'])
                );
            }

            if (isset($_POST['lpts_sendinblue_contact_attributes'])) {
                update_option(
                    Constants::LPTS_SENDINBLUE_ATTRIBUTES_OPTION,
                    $this->sanitizeUserAttributesFormFields($_POST['lpts_sendinblue_contact_attributes'])
                );
            }

            wp_safe_redirect(wp_get_referer());
            exit();
        }
    }

    /**
     * Sanitize datas of user attributes synch form
     *
     * @param array $datas Datas to sanitize.
     *
     * @return array|null
     * @since 1.0.0
     */
    private function sanitizeUserAttributesFormFields(array $datas): ?array
    {
        $fields = [];

        foreach ($datas as $k => $v) {
            $fields[$k] = sanitize_text_field($v);
        }

        return $fields;
    }

    /**
     * Display api key fields
     *
     * @return string|null
     * @since 1.0.0
     */
    public function apiKeyFieldRender(): ?string
    {
        return View::render('admin/options/partials/api-key-field', [
            'api_key_v3' => get_option(Constants::LPTS_API_KEY_V3_OPTION),
        ]);
    }

    /**
     * Display form
     *
     * @return string|null
     * @since 1.0.0
     */
    public function formRender(): ?string
    {
        $api_key = get_option(Constants::LPTS_API_KEY_V3_OPTION);
        $customer_attributes_option = get_option(Constants::LPTS_CUSTOMER_ATTRIBUTES_OPTION);
        $sendinblue_attributes_option = get_option(Constants::LPTS_SENDINBLUE_ATTRIBUTES_OPTION);

        $admin_profile = new WC_Admin_Profile();
        $customer_fields = $admin_profile->get_customer_meta_fields();
        $attrs = ApiManager::getAttributes();

        $contact_attributes = $attrs['attributes']['normal_attributes'];

        return View::render('admin/options/form', [
            'api_field_group' => self::LPTS_API_KEY_GROUP,
            'api_key' => $api_key,
            'customer_attributes_option' => $customer_attributes_option,
            'sendinblue_attributes_option' => $sendinblue_attributes_option,
            'customer_fields' => $customer_fields,
            'contact_attributes' => $contact_attributes,
            'nonce_action' => $this->nonce_action,
            'matched_attributes' => $this->getMatchedAttributes(),
        ]);
    }

    /**
     * Combine options Constants::LPTS_CUSTOMER_ATTRIBUTES_OPTION and Constants::LPTS_SENDINBLUE_ATTRIBUTES_OPTION and return result in
     * array
     *
     * @return array|null Array of matched attributes
     * @since 1.0.0
     */
    private function getMatchedAttributes(): ?array
    {
        return array_combine(
            get_option(Constants::LPTS_CUSTOMER_ATTRIBUTES_OPTION),
            get_option(Constants::LPTS_SENDINBLUE_ATTRIBUTES_OPTION)
        );
    }

    /**
     * Notice if the sendinblue API key is empty
     *
     * @wp-hook admin_notices
     * @return string|null
     * @since   1.0.0
     */
    public function apiKeyNotice(): ?string
    {
        if (empty(get_option(Constants::LPTS_API_KEY_V3_OPTION)) || !get_option(Constants::LPTS_API_KEY_V3_OPTION)) {
            return View::render('admin/options/partials/notice', []);
        }

        return null;
    }

    /**
     * Add plugin settings link under plugin name on plugins page
     *
     * @param array $links Array of links.
     *
     * @wp-hook plugin_action_links_ . __FILE__
     * @return array|null
     * @since   1.0.0
     */
    public function pluginActionLinks(array $links): ?array
    {
        $settings_links = [
            '<a href="' . admin_url('options-general.php?page=link_products_to_sendinblue') . '">' .
            esc_html__('Settings', 'link-products-to-sendinblue') . '</a>',
        ];

        return array_merge($settings_links, $links);
    }

    /**
     * Register main settings option
     *
     * @return void
     * @since 1.0.0
     */
    public function mainSettings(): void
    {
        false === get_option(Constants::LPTS_MAIN_OPTION) ? add_option(Constants::LPTS_MAIN_OPTION, []) : null;

        if (!empty(get_option(Constants::LPTS_API_KEY_V3_OPTION))) {
            $accoun_info = ApiManager::getAccountInfo();

            $args = [
                'account_email' => $accoun_info['account_email'],
                'access_key' => get_option(Constants::LPTS_API_KEY_V3_OPTION),
                'client_matched_attributes' => array_combine(
                    get_option(Constants::LPTS_SENDINBLUE_ATTRIBUTES_OPTION),
                    get_option(Constants::LPTS_CUSTOMER_ATTRIBUTES_OPTION)
                ),
            ];

            update_option(Constants::LPTS_MAIN_OPTION, $args);
        } else {
            update_option(Constants::LPTS_MAIN_OPTION, []);
        }
    }

    /**
     * Delete API KEY
     *
     * @return void
     * @since 1.0.0
     */
    public function deleteApiKey(): void
    {
        if (isset($_POST['lpts_delete_api_key'])) {
            delete_option(Constants::LPTS_API_KEY_V3_OPTION);

            wp_safe_redirect(wp_get_referer());
            exit();
        }
    }
}
