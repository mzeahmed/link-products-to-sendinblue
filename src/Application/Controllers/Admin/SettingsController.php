<?php

declare(strict_types=1);

namespace LPTS\Application\Controllers\Admin;

use LPTS\Application\Contract\PublicControllerInterface;
use LPTS\Infrastructure\WordPress\Hook\Admin\SettingsHook;

/**
 * @since 1.1.8
 */
class SettingsController implements PublicControllerInterface
{
    public function __construct(
        private SettingsHook $settingsHook
    ) {
    }

    public function register(): void
    {
        $this->settingsHook->register();
    }
}
