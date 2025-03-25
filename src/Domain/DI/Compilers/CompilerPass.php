<?php

declare(strict_types=1);

namespace LPTS\Domain\DI\Compilers;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 *  This trait processes tagged services and automatically adds method calls for registration.
 *  It is designed to be used during container bootstrapping (inspired by Symfony's compiler passes).
 *
 * @since 2.0.0
 */
trait CompilerPass
{
    /**
     * Processes tagged services and adds the specified method calls.
     *
     * @param ContainerBuilder $container The service container.
     * @param string $interface The interface that the tagged classes must implement.
     * @param string $tag The tag to look for in the service definitions.
     * @param string $param The container parameter name to store the IDs of tagged services.
     *
     * @return void
     * @since 2.2.0.78
     */
    public function boot(ContainerBuilder $container, string $interface, string $tag, string $param): void
    {
        $taggedServices = $container->findTaggedServiceIds($tag);

        // Store the service IDs in a container parameter
        $container->setParameter($param, array_keys($taggedServices));

        foreach ($taggedServices as $id => $tags) {
            $definition = $container->getDefinition($id);
            $class = $definition->getClass();

            // Check if the class implements the given interface
            if (is_a($class, $interface, true)) {
                $definition->addMethodCall('register', [new Reference($id)]);
            }
        }
    }
}
