<?php

declare(strict_types=1);

namespace LPTS\DI;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @since 1.1.8
 */
abstract class AbstractServicesContainer
{
    /**
     * Returns the path to the YAML configuration file.
     *
     * @return string Absolute path to the YAML file.
     */
    abstract protected static function getConfigPath(): string;

    /**
     * Returns the path to the container cache file.
     *
     * @return string Absolute path to the cache file.
     */
    abstract protected static function getCacheFile(): string;

    /**
     * Returns an instance of the cache container.
     *
     * @return mixed Instance of the cache container.
     */
    abstract protected static function getCacheContainer(): mixed;

    /**
     * Returns the directories watched for changes.
     *
     * @return array List of watched directories.
     */
    abstract protected static function getWatchedDirectories(): array;

    /**
     * Returns the namespace used for the generated container.
     *
     * @return string Namespace of the container.
     */
    abstract protected static function getNamespace(): string;

    /**
     * Returns the compiler passes to add to the container.
     *
     * @return array List of compiler passes.
     */
    abstract protected static function getCompilerPasses(): array;

    /**
     * Returns the service parameters to initialize.
     *
     * @return array List of service parameters.
     */
    abstract protected static function getServiceParameters(): array;

    /**
     * Loads the services into the container.
     *
     * This method decides whether to use the development or production container,
     * then initializes the services defined by the parameters.
     */
    public static function load(): void
    {
        $cacheContainerFile = static::getCacheFile();

        if (Debugger::isDevEnvironment()) {
            self::getDevelopmentContainer($cacheContainerFile, $container);
        } else {
            self::getProductionContainer($cacheContainerFile, $container);
        }

        foreach (static::getServiceParameters() as $parameter) {
            self::initializeServices($container, $parameter);
        }
    }

    /**
     * Loads the container in production.
     *
     * @param string $cacheContainerFile Path to the cache file.
     * @param mixed $container Service container.
     *
     * @throws \RuntimeException If the cache file is not found.
     */
    private static function getProductionContainer(string $cacheContainerFile, &$container): void
    {
        if (file_exists($cacheContainerFile)) {
            require_once $cacheContainerFile;
            $container = static::getCacheContainer();
        } else {
            throw new \RuntimeException("The container cache file was not found in production");
        }
    }

    /**
     * Builds or loads a dependency container for the development environment.
     *
     * This method checks if a cache file for the container exists and is up to date.
     * If the cache file is outdated or does not exist, the container is rebuilt.
     * Otherwise, the container is loaded from the cache file.
     *
     * @param string $cacheContainerFile Path to the cache file.
     * @param mixed $container Service container.
     */
    private static function getDevelopmentContainer(string $cacheContainerFile, &$container): void
    {
        $configFile = static::getConfigPath();

        // Checks if the container needs to be rebuilt –
        //  If the cache file does not exist.
        // - Or if the configuration file was modified after the cache file.
        $needsRebuild = !file_exists($cacheContainerFile) || filemtime($configFile) > filemtime($cacheContainerFile);

        // List to track files in the watched directories.
        $watchedFiles = [];

        // Checks if any of the watched files were modified after the cache file.
        foreach (static::getWatchedDirectories() as $directory) {
            foreach (ysFile()->listFilesWithRecursiveIteratorIterator($directory) as $file) {
                $watchedFiles[] = $file;
            }
        }

        foreach ($watchedFiles as $watchedFile) {
            if (file_exists($watchedFile) && filemtime($watchedFile) > filemtime($cacheContainerFile)) {
                $needsRebuild = true;
                break;
            }
        }

        if ($needsRebuild) {
            $container = new ContainerBuilder();
            $loader = new YamlFileLoader($container, new FileLocator($configFile));

            try {
                $loader->load($configFile);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }

            self::addCompilerPasses($container);

            $container->compile();

            $dumper = new PhpDumper($container);
            file_put_contents(
                $cacheContainerFile,
                $dumper->dump([
                    'class' => 'CacheContainer',
                    'namespace' => static::getNamespace(),
                ])
            );
        } else {
            require_once $cacheContainerFile;
            $container = static::getCacheContainer();
        }
    }

    /**
     * Adds the compiler passes to the container.
     *
     * @param ContainerBuilder $container The service container.
     */
    private static function addCompilerPasses(ContainerBuilder $container): void
    {
        foreach (static::getCompilerPasses() as $compilerPass) {
            $container->addCompilerPass($compilerPass);
        }
    }

    /**
     * Initializes the services for a given parameter.
     *
     * @param mixed $container The service container.
     * @param string $parameter Name of the parameter containing the services to initialize.
     */
    private static function initializeServices(mixed $container, string $parameter): void
    {
        $serviceIds = $container->getParameter($parameter);
        foreach ($serviceIds as $id) {
            try {
                $container->get($id);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}
