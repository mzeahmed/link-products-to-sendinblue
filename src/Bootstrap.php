<?php

declare(strict_types=1);

namespace LPTS;

use LPTS\Domain\DI\ServicesContainer;
use MzeAhmed\WpToolKit\Traits\Singleton;
use LPTS\Infrastructure\WordPress\I18n\I18n;

/**
 * @package LPTS
 * @since   1.0.0
 */
final class Bootstrap
{
    use Singleton;

    public function __construct()
    {
        $this->boot();
    }

    private function boot(): void
    {
        add_action('plugins_loaded', static function () {
            if (function_exists('WC')) {
                I18n::load();
                ServicesContainer::load();

                do_action('lpts_plugins_loaded');
            }
        });
    }
}
