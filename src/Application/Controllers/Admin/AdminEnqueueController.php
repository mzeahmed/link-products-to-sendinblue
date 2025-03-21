<?php

declare(strict_types=1);

namespace LPTS\Application\Controllers\Admin;

use LPTS\Application\Contract\AdminControllerInterface;
use LPTS\Infrastructure\WordPress\Hook\Admin\AdminEnqueueHook;

/**
 * @since 1.2.0
 */
class AdminEnqueueController implements AdminControllerInterface
{
    public function __construct(
        private AdminEnqueueHook $adminEnqueueHook
    ) {
    }

    public function register(): void
    {
        $this->adminEnqueueHook->register();
    }
}
