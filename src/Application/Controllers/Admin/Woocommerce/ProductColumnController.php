<?php

declare(strict_types=1);

namespace LPTS\Application\Controllers\Admin\Woocommerce;

use LPTS\Application\Contract\AdminControllerInterface;
use LPTS\Infrastructure\WordPress\Hook\Admin\Woocommerce\ProductColumnHook;

/**
 * Add custom column to product list
 *
 * @package LPTS\Admin\Woocommerce
 * @since   1.0.0
 */
class ProductColumnController implements AdminControllerInterface
{
    public function __construct(
        private ProductColumnHook $customProductColumnHook
    ) {}

    public function register()
    {
        $this->customProductColumnHook->register();
    }
}
