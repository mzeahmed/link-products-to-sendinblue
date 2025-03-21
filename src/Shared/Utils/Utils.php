<?php

declare(strict_types=1);

namespace LPTS\Shared\Utils;

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
}
