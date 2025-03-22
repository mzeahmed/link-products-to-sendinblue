<?php

declare(strict_types=1);

namespace LPTS\Domain\Services\Admin;

use LPTS\Shared\Utils\Utils;
use LPTS\Shared\Enums\OptionKey;
use LPTS\Infrastructure\View\Renderer;
use LPTS\Infrastructure\External\Brevo\ApiManager;

/**
 * @since 1.2.0
 */
class SettingsService
{
    private const string LPTS_API_KEY_GROUP = 'link_products_to_sendinblue';

    private string $nonce_action;

    public function __construct(
        private Renderer $renderer
    ) {
        $this->nonce_action = admin_url(
            'options-general.php?page=link_products_to_sendinblue&tab=user_attributes'
        );
    }

    public function addOptionsPage(): void
    {
        add_options_page(
            __('Link Products To Sendinblue Lists From WooCommerce settings', 'link-products-to-sendinblue'),
            __('Link Products To Sendinblue Lists From WooCommerce', 'link-products-to-sendinblue'),
            'manage_options',
            self::LPTS_API_KEY_GROUP,
            [$this, 'formRender']
        );
    }

    public function addminInit(): void
    {
        $this->registerApiKeySettingsSection();
        $this->saveUserAttributes();
        $this->mainSettings();
        $this->deleteApiKey();
    }

    public function pluginActionLinks(array $links): ?array
    {
        $settings_links = [
            '<a href="' . admin_url('options-general.php?page=link_products_to_sendinblue') . '">' .
            esc_html__('Settings', 'link-products-to-sendinblue') . '</a>',
        ];

        return array_merge($settings_links, $links);
    }

    public function apiKeyNotice(): void
    {
        if (empty(Utils::getApiKey())) {
            echo $this->renderer->render('admin/options/partials/notice', []);
        }
    }

    public function formRender(): void
    {
        $apiKey = Utils::getApiKey();
        $customerAttributes = get_option(OptionKey::CUSTOMER_ATTRIBUTES->value);
        $brevoAttributes = get_option(OptionKey::BREVO_ATTRIBUTES->value);

        $adminProfile = new \WC_Admin_Profile();
        $customerMetaFields = $adminProfile->get_customer_meta_fields();
        $attrs = ApiManager::getAttributes();

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

    private function saveUserAttributes(): void
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
                    OptionKey::CUSTOMER_ATTRIBUTES->value,
                    $this->sanitizeUserAttributesFormFields($_POST['lpts_woocommerce_customer_attributes'])
                );
            }

            if (isset($_POST['lpts_sendinblue_contact_attributes'])) {
                update_option(
                    OptionKey::BREVO_ATTRIBUTES->value,
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
     * @return void
     * @since 1.2.0
     */
    private function registerApiKeySettingsSection(): void
    {
        register_setting(self::LPTS_API_KEY_GROUP, OptionKey::API_KEY_V3->value);

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

    private function deleteApiKey(): void
    {
        if (isset($_POST['lpts_delete_api_key'])) {
            delete_option(OptionKey::API_KEY_V3->value);

            wp_safe_redirect(wp_get_referer());
            exit();
        }
    }

    private function mainSettings(): void
    {
        if (!empty(get_option(OptionKey::API_KEY_V3->value))) {
            $accoun_info = ApiManager::getAccountInfo();

            $args = [
                'account_email' => $accoun_info['account_email'],
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
    private function getMatchedAttributes(): ?array
    {
        return array_combine(
            get_option(OptionKey::CUSTOMER_ATTRIBUTES->value),
            get_option(OptionKey::BREVO_ATTRIBUTES->value)
        );
    }
}
