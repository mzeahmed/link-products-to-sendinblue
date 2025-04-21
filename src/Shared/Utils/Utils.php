<?php

declare(strict_types=1);

namespace LPTS\Shared\Utils;

use LPTS\Shared\Enums\OptionKey;

/**
 * @since 2.0.0
 */
class Utils
{
    /**
     * Check if the current environment is development.
     *
     * @return bool
     */
    public static function isDevEnvironment()
    {
        return \defined('WP_ENV') && 'development' === WP_ENV;
    }

    /**
     * Get the API key.
     *
     * @return string
     */
    public static function getApiKey()
    {
        return defined('LPTS_API_KEY') ? LPTS_API_KEY : (get_option(OptionKey::API_KEY_V3->value) ?? '');
    }

    public static function versionCompare()
    {
        $errors = static function ($message, string $subtitle = '', string $title = '') {
            $title = $title ? esc_html__('Error', 'link-products-to-sendinblue') : '';
            $message = "<h1>{$title}<br><small>{$subtitle}</small></h1><p>{$message}</p>";
            wp_die($message);
        };

        /** Ensure PHP version compatibility */
        if (version_compare('8.0', PHP_VERSION, '>=')) {
            $errors(
                esc_html__(
                    'To use Link Products to Sendinblue Lists From WooCommerce plugin, please use php 8.0 or higher',
                    'link-products-to-sendinblue'
                ),
                esc_html__('Incompatible PHP version', 'link-products-to-sendinblue')
            );
        }

        /** Ensure WordPress version compatibility*/
        // @phpstan-ignore-next-line
        if (version_compare('5.1', get_bloginfo('version'), '>=')) {
            $errors(
                esc_html__(
                    'To use Link Products to Sendinblue Lists From WooCommerce plugin, please install WordPress 5.1 or higher',
                    'link-products-to-sendinblue'
                ),
                esc_html__('Incompatible WordPress version', 'link-products-to-sendinblue')
            );
        }

        return true;
    }
}
