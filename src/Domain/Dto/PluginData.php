<?php

declare(strict_types=1);

namespace LPTS\Domain\Dto;

/**
 * Represents the metadata and information required for the plugin.
 * It contains details about the plugin such as path, version, author,
 * as well as minimum dependencies (PHP, WordPress).
 *
 * @since 1.1.8
 */
class PluginData
{
    public string $pluginPath;
    public string $templatePath;
    public string $name;
    public string $uri;
    public string $description;
    public string $version;
    public string $author;
    public string $authorUri;
    public string $textDomain;
    public string $domainPath;
    public string $requiredPhp;
    public string $requiredWp;
    public string $namespace;

    public function __construct(
        string $pluginPath,
        string $templatePath,
        string $name,
        string $uri,
        string $description,
        string $version,
        string $author,
        string $authorUri,
        string $textDomain,
        string $domainPath,
        string $requiredPhp,
        string $requiredWp,
        string $namespace
    ) {
        $this->pluginPath = $pluginPath;
        $this->templatePath = $templatePath;
        $this->name = $name;
        $this->uri = $uri;
        $this->description = $description;
        $this->version = $version;
        $this->author = $author;
        $this->authorUri = $authorUri;
        $this->textDomain = $textDomain;
        $this->domainPath = $domainPath;
        $this->requiredPhp = $requiredPhp;
        $this->requiredWp = $requiredWp;
        $this->namespace = $namespace;
    }
}
