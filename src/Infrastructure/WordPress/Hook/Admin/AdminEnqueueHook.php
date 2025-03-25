<?php

declare(strict_types=1);

namespace LPTS\Infrastructure\WordPress\Hook\Admin;

use LPTS\Application\Contract\HookInterface;
use LPTS\Domain\Services\Admin\AdminEnqueueService;

/**
 * @since 2.0.0
 */
class AdminEnqueueHook implements HookInterface
{
    public function __construct(
        private AdminEnqueueService $adminEnqueueService
    ) {}

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
    }

    /**
     * Register plugin scripts and styles
     *
     * @param string $hook The current admin page.
     *
     * @return void
     * @since 1.1.5
     */
    public function adminEnqueueScripts(string $hook): void
    {
        $this->adminEnqueueService->adminEnqueueScripts($hook);
    }
}
