<?php

declare(strict_types=1);

namespace LPTS\Infrastructure;

use LPTS\Domain\Dto\PluginData;
use MzeAhmed\WpToolKit\Traits\Singleton;

/**
 * Plugin data holder. Most values are defined
 * from the plugin header. This class is used
 * to inject metadata into each class that extends the Base class.
 *
 * @since 1.1.8
 */
final class Plugin
{
    use Singleton;

    /**
     * Retrieves plugin metadata and returns it as a PluginData instance.
     *
     * This method fetches plugin information from the plugin header
     * and merges it with default paths (plugin path, template path).
     * Filters can be applied to modify the metadata before creating the PluginData object.
     *
     * @return PluginData Instance containing all plugin metadata and information.
     *
     * @since 1.1.8
     */
    public function getData(): PluginData
    {
        $pluginData = [
            'plugin_path' => untrailingslashit(plugin_dir_path(LPTS_PLUGIN_FILE)),
            'template_path' => untrailingslashit(plugin_dir_path(LPTS_PLUGIN_FILE) . 'resources/views'),
        ];

        $pluginFileHeaders = [
            'name' => 'Plugin Name',
            'uri' => 'Plugin URI',
            'description' => 'Description',
            'version' => 'Version',
            'author' => 'Author',
            'author-uri' => 'Author URI',
            'text-domain' => 'Text Domain',
            'domain-path' => 'Domain Path',
            'required-php' => 'Requires PHP',
            'required-wp' => 'Requires WP',
            'namespace' => 'Namespace',
        ];

        $fileData = get_file_data(LPTS_PLUGIN_FILE, $pluginFileHeaders, 'plugin') ?? [];

        $datas = array_merge($fileData, $pluginData);

        $datas = apply_filters('ys_plugin_data', $datas);

        return new PluginData(
            $datas['plugin_path'] ?? '',
            $datas['template_path'] ?? '',
            $datas['name'] ?? '',
            $datas['uri'] ?? '',
            $datas['description'] ?? '',
            $datas['version'] ?? '',
            $datas['author'] ?? '',
            $datas['author-uri'] ?? '',
            $datas['text-domain'] ?? '',
            $datas['domain-path'] ?? '',
            $datas['required-php'] ?? '',
            $datas['required-wp'] ?? '',
            $datas['namespace'] ?? ''
        );
    }

    /** @since 1.1.8 */
    public function getPluginPath(): string
    {
        return $this->getData()->pluginPath;
    }

    /** @since 1.1.8 */
    public function getTemplatePath(): string
    {
        return $this->getData()->templatePath;
    }

    /** @since 1.1.8 */
    public function getVersion(): string
    {
        return $this->getData()->version;
    }

    /** @since 1.1.8 */
    public function getRequiredPhp(): string
    {
        return $this->getData()->requiredPhp;
    }

    /** @since 1.1.8 */
    public function getRequiredWp(): string
    {
        return $this->getData()->requiredWp;
    }

    /** @since 1.1.8 */
    public function getName(): string
    {
        return $this->getData()->name;
    }

    /** @since 1.1.8 */
    public function getUri(): string
    {
        return $this->getData()->uri;
    }

    /** @since 1.1.8 */
    public function getDescription(): string
    {
        return $this->getData()->description;
    }

    /** @since 1.1.8 */
    public function getAuthor(): string
    {
        return $this->getData()->author;
    }

    /** @since 1.1.8 */
    public function getAuthorUri(): string
    {
        return $this->getData()->authorUri;
    }

    /** @since 1.1.8 */
    public function getTextDomain(): string
    {
        return $this->getData()->textDomain;
    }

    /** @since 1.1.8 */
    public function getDomainPath(): string
    {
        return $this->getData()->domainPath;
    }

    /** @since 1.1.8 */
    public function getNamespace(): string
    {
        return $this->getData()->namespace;
    }
}
