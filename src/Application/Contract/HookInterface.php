<?php

declare(strict_types=1);

namespace LPTS\Application\Contract;

/**
 * Interface for WordPress hooks registration.
 *
 * @since 1.2.0
 */
interface HookInterface
{
    /**
     * Register WordPress hooks.
     *
     * All hooks (add_action, add_filter, etc.) must be registered inside this method.
     *
     * @return void
     */
    public function register(): void;
}
