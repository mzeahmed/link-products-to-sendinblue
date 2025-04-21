<?php

declare(strict_types=1);

namespace LPTS\Domain\DI\Compilers;

use LPTS\Application\Contract\AdminControllerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * @since 2.0.0
 */
class AdminControllerPass implements CompilerPassInterface
{
    use CompilerPass;

    public function process(ContainerBuilder $container)
    {
        $this->boot($container, AdminControllerInterface::class, 'app.admin.controller', 'app.admin_controller_ids');
    }
}
