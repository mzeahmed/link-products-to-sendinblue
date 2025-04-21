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
    public function getData()
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

    /**
     * @return string The path to the plugin directory.
     * @since 1.1.8
     */
    public function getPluginPath()
    {
        return $this->getData()->pluginPath;
    }

    /**
     * @return string The path to the plugin's template directory.
     * @since 1.1.8
     */
    public function getTemplatePath()
    {
        return $this->getData()->templatePath;
    }

    /**
     * @return string The version of the plugin.
     * @since 1.1.8
     */
    public function getVersion()
    {
        return $this->getData()->version;
    }

    /**
     * @return string The required PHP version.
     * @since 1.1.8
     */
    public function getRequiredPhp()
    {
        return $this->getData()->requiredPhp;
    }

    /**
     * @return string The required WordPress version.
     * @since 1.1.8
     */
    public function getRequiredWp()
    {
        return $this->getData()->requiredWp;
    }

    /**
     * @return string The name of the plugin.
     * @since 1.1.8
     */
    public function getName()
    {
        return $this->getData()->name;
    }

    /**
     * @return string The plugin URI.
     * @since 1.1.8
     */
    public function getUri()
    {
        return $this->getData()->uri;
    }

    /**
     * @return string The description of the plugin.
     * @since 1.1.8
     */
    public function getDescription()
    {
        return $this->getData()->description;
    }

    /**
     * @return string The author of the plugin.
     * @since 1.1.8
     */
    public function getAuthor()
    {
        return $this->getData()->author;
    }

    /**
     * @return string The author URI of the plugin.
     * @since 1.1.8
     */
    public function getAuthorUri()
    {
        return $this->getData()->authorUri;
    }

    /**
     * @return string The text domain of the plugin.
     * @since 1.1.8
     */
    public function getTextDomain()
    {
        return $this->getData()->textDomain;
    }

    /**
     * @return string The domain path of the plugin.
     * @since 1.1.8
     */
    public function getDomainPath()
    {
        return $this->getData()->domainPath;
    }

    /**
     * @return string The namespace of the plugin.
     * @since 1.1.8
     */
    public function getNamespace()
    {
        return $this->getData()->namespace;
    }
}
