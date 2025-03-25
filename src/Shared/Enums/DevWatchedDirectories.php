<?php

declare(strict_types=1);

namespace LPTS\Shared\Enums;

/**
 * @since 2.0.0
 */
enum DevWatchedDirectories: string
{
    case CONTROLLERS = '/dev/plugins/link-products-to-sendinblue/src/Application/Controllers';
    case COMPILERS = '/dev/plugins/link-products-to-sendinblue/src/Domain/DI/Compilers';
    case SERVICES = '/dev/plugins/link-products-to-sendinblue/src/Domain/Services';

    public function fullPath(): string
    {
        return APP_ABS_PATH . $this->value;
    }

    public static function getAlls(): array
    {
        return array_map(
            static fn(self $case) => $case->fullPath(),
            self::cases()
        );
    }
}
