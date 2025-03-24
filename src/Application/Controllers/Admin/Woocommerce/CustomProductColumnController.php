<?php

declare(strict_types=1);

namespace LPTS\Application\Controllers\Admin\Woocommerce;

use LPTS\Application\Contract\AdminControllerInterface;
use LPTS\Infrastructure\WordPress\Hook\Admin\Woocommerce\CustomProductColumnHook;

/**
 * Add custom column to product list
 *
 * @package LPTS\Admin\Woocommerce
 * @since   1.0.0
 */
class CustomProductColumnController implements AdminControllerInterface
{
    public function __construct(
        private CustomProductColumnHook $customProductColumnHook
    ) {
    }

    public function register(): void
    {
        $this->customProductColumnHook->register();
    }
}
