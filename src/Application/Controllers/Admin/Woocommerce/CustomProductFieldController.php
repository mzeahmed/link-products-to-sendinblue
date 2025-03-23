<?php

declare(strict_types=1);

namespace LPTS\Application\Controllers\Admin\Woocommerce;

use LPTS\Application\Contract\AdminControllerInterface;
use LPTS\Infrastructure\WordPress\Hook\Admin\Woocommerce\CustomProductFielHook;

/**
 * Add a custom field to the product page
 *
 * @package LPTS\Admin\Woocommerce
 * @since   1.0.0
 */
class CustomProductFieldController implements AdminControllerInterface
{
    public function __construct(
        private CustomProductFielHook $customProductFielHook
    ) {}

    public function register(): void
    {
        $this->customProductFielHook->register();
    }
}
