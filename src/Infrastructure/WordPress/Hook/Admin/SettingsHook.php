<?php

declare(strict_types=1);

namespace LPTS\Infrastructure\WordPress\Hook\Admin;

use LPTS\Domain\Services\SettingsService;
use LPTS\Application\Contract\HookInterface;

class SettingsHook implements HookInterface
{
    public function __construct(
        private SettingsService $settingsService
    ) {}

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        add_action('admin_menu', [$this, 'addOptionsPage']);
        // add_action('admin_init', [$this, 'registerApiKeySettingsSection']);
        // add_action('admin_init', [$this, 'saveUserAttributes']);

        add_action('admin_notices', [$this, 'apiKeyNotice']);
        add_filter('plugin_action_links_' . LPTS_PLUGIN_BASENAME, [$this, 'pluginActionLinks']);

        // add_action('admin_init', [$this, 'mainSettings']);
        // add_action('admin_init', [$this, 'deleteApiKey']);

        add_action('admin_init', [$this, 'addminInit']);
    }

    /**
     * Add options menu
     *
     * @wp-hook admin_menu
     * @since   1.0.0
     */
    public function addOptionsPage(): void
    {
        $this->settingsService->addOptionsPage();
    }

    public function addminInit(): void
    {
        $this->settingsService->addminInit();
    }

    /**
     * Display form
     *
     * @return void
     * @since 1.0.0
     */
    public function formRender(): void
    {
        $this->settingsService->formRender();
    }

    /**
     * Notice if the sendinblue API key is empty
     *
     * @wp-hook admin_notices
     * @return void
     * @since   1.0.0
     */
    public function apiKeyNotice(): void
    {
        $this->settingsService->apiKeyNotice();
    }

    /**
     * Add plugin settings link under plugin name on plugins page
     *
     * @param array $links Array of links.
     *
     * @wp-hook plugin_action_links_ . __FILE__
     * @return array|null
     * @since   1.0.0
     */
    public function pluginActionLinks(array $links): ?array
    {
        return $this->settingsService->pluginActionLinks($links);
    }
}
