<?php

declare(strict_types=1);

namespace LPTS\Infrastructure\WordPress\Hook\Admin;

use LPTS\Infrastructure\Plugin;
use LPTS\Application\Contract\HookInterface;

/**
 * @since 1.2.0
 */
class AdminEnqueueHook implements HookInterface
{
    private Plugin $plugin;

    public function __construct()
    {
        $this->plugin = Plugin::getInstance();
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
    }

    /**
     * Register plugin scripts and styles
     *
     * @return void
     * @since 1.1.5
     */
    public function adminEnqueueScripts(): void
    {
        $asset = require LPTS_PLUGIN_PATH . 'resources/build/admin-app.asset.php';

        $dependencies = array_merge($asset['dependencies'] ?? [], ['jquery', 'wp-element']);

        if (isset($_GET['page']) && 'link_products_to_sendinblue' === $_GET['page']) {
            wp_enqueue_style(
                str_replace(' ', '-', strtolower($this->plugin->getName())) . '-admin-app',
                plugin_dir_url(LPTS_PLUGIN_FILE) . 'resources/build/admin-app.css',
                [],
                (string) filemtime(LPTS_PLUGIN_PATH . 'resources/build/admin-app.css')
            );

            if (isset($_GET['tab']) && 'user_attributes' === $_GET['tab']) {
                wp_enqueue_script(
                    str_replace(' ', '-', strtolower($this->plugin->getName())) . '-admin-app',
                    plugin_dir_url(LPTS_PLUGIN_FILE) . 'resources/build/admin-app.js',
                    $dependencies,
                    (string) filemtime(LPTS_PLUGIN_PATH . 'resources/build/admin-app.js'),
                    true
                );

                wp_set_script_translations(
                    str_replace(' ', '-', strtolower($this->plugin->getName())) . '-admin-app',
                    'link-products-to-sendinblue',
                    plugin_dir_path(LPTS_PLUGIN_FILE) . 'resources/i18n/'
                );
            }
        }
    }
}
