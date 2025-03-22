<?php

declare(strict_types=1);

namespace LPTS\Shared\Utils;

use LPTS\Shared\Enums\OptionKey;

/**
 * @since 1.2.0
 */
class Utils
{
    /**
     * Check if the current environment is development.
     *
     * @return bool
     */
    public static function isDevEnvironment(): bool
    {
        return \defined('WP_ENV') && 'development' === WP_ENV;
    }

    /**
     * Get the API key.
     *
     * @return string
     */
    public static function getApiKey(): string
    {
        return defined('LPTS_API_KEY') ? LPTS_API_KEY : get_option(OptionKey::API_KEY_V3->value);
    }
}
