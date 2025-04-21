<?php

declare(strict_types=1);

namespace LPTS\Domain\Services\Admin;

use LPTS\Infrastructure\Plugin;

/**
 * @since 2.0.0
 */
class AdminEnqueueService
{
    private Plugin $plugin;

    public function __construct()
    {
        $this->plugin = Plugin::getInstance();
    }

    public function adminEnqueueScripts(string $hook)
    {
        global $post;

        $asset = require LPTS_PLUGIN_PATH . 'resources/build/admin-app.asset.php';

        $dependencies = array_merge($asset['dependencies'] ?? [], ['jquery', 'wp-element']);

        $postType = get_post_type($post);
        $isProductPage = 'product' === $postType && ('post.php' === $hook || 'post-new.php' === $hook);

        if ('settings_page_link_products_to_sendinblue' === $hook || $isProductPage) {
            wp_enqueue_style(
                str_replace(' ', '-', strtolower($this->plugin->getName())) . '-admin-app',
                plugin_dir_url(LPTS_PLUGIN_FILE) . 'resources/build/admin-app.css',
                [],
                (string) filemtime(LPTS_PLUGIN_PATH . 'resources/build/admin-app.css')
            );

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
