<?php

declare(strict_types=1);

namespace LPTS\Domain\Services\Admin;

use LPTS\Shared\Utils\Utils;
use LPTS\Shared\Enums\OptionKey;
use LPTS\Infrastructure\View\Renderer;
use MzeAhmed\WpToolKit\Utils\Sanitizer;
use LPTS\Infrastructure\External\Brevo\BrevoManager;

/**
 * @since 2.0.0
 */
class SettingsService
{
    private const LPTS_API_KEY_GROUP = 'link_products_to_sendinblue';

    private string $nonce_action;

    public function __construct(
        private Renderer $renderer
    ) {
        $this->nonce_action = admin_url(
            'options-general.php?page=link_products_to_sendinblue&tab=user_attributes'
        );
    }

    public function addOptionsPage()
    {
        add_options_page(
            __('Link Products To Sendinblue Lists From WooCommerce settings', 'link-products-to-sendinblue'),
            __('Link Products To Sendinblue Lists From WooCommerce', 'link-products-to-sendinblue'),
            'manage_options',
            self::LPTS_API_KEY_GROUP,
            [$this, 'formRender']
        );
    }

    public function addminInit()
    {
        $this->registerApiKeySettingsSection();
        $this->saveUserAttributes();
        $this->mainSettings();
        $this->deleteApiKey();
    }

    public function pluginActionLinks(array $links)
    {
        $settings_links = [
            '<a href="' . admin_url('options-general.php?page=link_products_to_sendinblue') . '">' .
            esc_html__('Settings', 'link-products-to-sendinblue') . '</a>',
        ];

        return array_merge($settings_links, $links);
    }

    public function apiKeyNotice()
    {
        if (empty(Utils::getApiKey())) {
            echo $this->renderer->render('admin/options/partials/notice', []);
        }
    }

    public function formRender()
    {
        $apiKey = Utils::getApiKey();
        $customerAttributes = get_option(OptionKey::CUSTOMER_ATTRIBUTES->value);
        $brevoAttributes = get_option(OptionKey::BREVO_ATTRIBUTES->value);

        $adminProfile = new \WC_Admin_Profile();
        $customerMetaFields = $adminProfile->get_customer_meta_fields();
        $attrs = BrevoManager::getAttributes();

        $contact_attributes = $attrs['attributes']['normal_attributes'];

        echo $this->renderer->render('admin/options/form', [
            'api_field_group' => self::LPTS_API_KEY_GROUP,
            'api_key' => $apiKey,
            'customer_attributes_option' => $customerAttributes,
            'sendinblue_attributes_option' => $brevoAttributes,
            'customer_fields' => $customerMetaFields,
            'contact_attributes' => $contact_attributes,
            'nonce_action' => $this->nonce_action,
            'matched_attributes' => $this->getMatchedAttributes(),
        ]);
    }

    /**
     * Display api key fields
     *
     * @return void
     * @since 1.0.0
     */
    public function apiKeyFieldRender(): void
    {
        echo $this->renderer->render('admin/options/partials/api-key-field', [
            'api_key_v3' => Utils::getApiKey(),
        ]);
    }

    private function saveUserAttributes()
    {
        /**
         * Options
         */
        $customer_attributes = get_option(OptionKey::CUSTOMER_ATTRIBUTES->value);
        $contact_attributes = get_option(OptionKey::BREVO_ATTRIBUTES->value);

        /**
         * Add options if they don't exists
         */
        false === $customer_attributes ? add_option(OptionKey::CUSTOMER_ATTRIBUTES->value, []) : null;
        false === $contact_attributes ? add_option(OptionKey::BREVO_ATTRIBUTES->value, []) : null;

        if (isset($_POST['_user_attributes_nonce'])) {
            if (!wp_verify_nonce($_POST['_user_attributes_nonce'], $this->nonce_action)) {
                wp_die(
                    sprintf(
                        esc_html__('Sorry, nonce %s did not verify.', 'link-products-to-sendinblue'),
                        '_user_attributes_nonce'
                    )
                );
            }

            if (isset($_POST['lpts_woocommerce_customer_attributes'])) {
                update_option(
                    OptionKey::CUSTOMER_ATTRIBUTES->value,
                    Sanitizer::text($_POST['lpts_woocommerce_customer_attributes'])
                );
            }

            if (isset($_POST['lpts_sendinblue_contact_attributes'])) {
                update_option(
                    OptionKey::BREVO_ATTRIBUTES->value,
                    Sanitizer::text($_POST['lpts_sendinblue_contact_attributes'])
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
     * @depecated Use Utils::sanitizeDatas instead
     */
    private function sanitizeUserAttributesFormFields(array $datas)
    {
        $fields = [];

        foreach ($datas as $k => $v) {
            $fields[$k] = sanitize_text_field($v);
        }

        return $fields;
    }

    /**
     * @return void
     * @since 2.0.0
     */
    private function registerApiKeySettingsSection()
    {
        register_setting(self::LPTS_API_KEY_GROUP, OptionKey::API_KEY_V3->value);

        add_settings_section(
            'api_key_section',
            __('API v3 Access key', 'link-products-to-sendinblue'),
            static function () {
                echo \sprintf(
                    '<p><a href="%s" target="_blank">%s</a></p>',
                    esc_url('https://app.brevo.com/settings/keys/api'),
                    esc_html__('Get your account API key', 'link-products-to-sendinblue')
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

    private function deleteApiKey()
    {
        if (isset($_POST['lpts_delete_api_key'])) {
            delete_option(OptionKey::API_KEY_V3->value);

            wp_safe_redirect(wp_get_referer());
            exit();
        }
    }

    private function mainSettings()
    {
        if (!empty(get_option(OptionKey::API_KEY_V3->value))) {
            $accountInfo = BrevoManager::getAccountInfo();

            $args = [
                'account_email' => $accountInfo['account_email'],
                'access_key' => get_option(OptionKey::API_KEY_V3->value),
                'client_matched_attributes' => array_combine(
                    get_option(OptionKey::BREVO_ATTRIBUTES->value),
                    get_option(OptionKey::CUSTOMER_ATTRIBUTES->value)
                ),
            ];

            update_option(OptionKey::MAIN_OPTION->value, $args);
        } else {
            update_option(OptionKey::MAIN_OPTION->value, []);
        }
    }

    /**
     * Combine options OptionKey::CUSTOMER_ATTRIBUTES->value and OptionKey::SENDINBLUE_ATTRIBUTES->$this->value and return result in
     * array
     *
     * @return array|null Array of matched attributes
     * @since 1.0.0
     */
    private function getMatchedAttributes()
    {
        return array_combine(
            get_option(OptionKey::CUSTOMER_ATTRIBUTES->value),
            get_option(OptionKey::BREVO_ATTRIBUTES->value)
        );
    }
}
