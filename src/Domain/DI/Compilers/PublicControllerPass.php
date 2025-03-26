<?php

declare(strict_types=1);

namespace LPTS\Domain\DI\Compilers;

use LPTS\Application\Contract\PublicControllerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * @since 2.0.0
 */
class PublicControllerPass implements CompilerPassInterface
{
    use CompilerPass;

    public function process(ContainerBuilder $container)
    {
        $this->boot($container, PublicControllerInterface::class, 'app.public.controller', 'app.public_controller_ids');
    }
}
