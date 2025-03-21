<?php

declare(strict_types=1);

namespace LPTS\Domain\DI;

use LPTS\Shared\Enums\DevWatchedDirectories;
use LPTS\Domain\DI\Compilers\AdminControllerPass;
use LPTS\Domain\DI\Compilers\PublicControllerPass;

/**
 * @since 1.1.8
 */
class ServicesContainer extends AbstractServicesContainer
{
    /**
     * @inheritDoc
     */
    protected static function getConfigPath(): string
    {
        if (WP_ENV === 'development') {
            return APP_ABS_PATH . '/dev/plugins/link-products-to-sendinblue/config/services.yml'; // @phpstan-ignore-line
        }

        return LPTS_PLUGIN_PATH . 'config/services.yml';
    }

    /**
     * @inheritDoc
     */
    protected static function getCacheFile(): string
    {
        if (WP_ENV === 'development') {
            return APP_ABS_PATH . '/dev/plugins/link-products-to-sendinblue/src/Domain/DI/CacheContainer.php'; // @phpstan-ignore-line
        }

        return __DIR__ . '/CacheContainer.php';
    }

    /**
     * @inheritDoc
     */
    protected static function getCacheContainer(): CacheContainer
    {
        return new CacheContainer();
    }

    /**
     * @inheritDoc
     */
    protected static function getWatchedDirectories(): array
    {
        return DevWatchedDirectories::getAlls();
    }

    /**
     * @inheritDoc
     */
    protected static function getNamespace(): string
    {
        return 'LPTS\Domain\DI';
    }

    /**
     * @inheritDoc
     */
    protected static function getCompilerPasses(): array
    {
        return [
            new PublicControllerPass(),
            new AdminControllerPass(),
        ];
    }

    /**
     * @inheritDoc
     */
    protected static function getServiceParameters(): array
    {
        return [
            'app.public_controller_ids',
            'app.admin_controller_ids',
        ];
    }
}
