<?php

declare(strict_types=1);

/**
 * PHPStan bootstrap file.
 *
 * This file is used to ensure the correct order of file inclusion for PHPStan analysis.
 * It prevents false-positive errors such as "Undefined constant 'LPTS_PLUGIN_PATH'" or similar.
 *
 * @since 1.2.0
 */

if (!defined('APP_ABS_PATH')) {
    define('APP_ABS_PATH', dirname(__DIR__, 3));
}

require_once __DIR__ . '/link-products-to-sendinblue.php';
