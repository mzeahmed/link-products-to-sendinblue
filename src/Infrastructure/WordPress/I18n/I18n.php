<?php

declare(strict_types=1);

namespace LPTS\Infrastructure\WordPress\I18n;

/**
 * @since 1.2.0
 */
class I18n
{
    public static function load(): void
    {
        load_plugin_textdomain(
            'link-products-to-sendinblue',
            false,
            plugin_dir_path(LPTS_PLUGIN_FILE) . 'resources/i18n/'
        );
    }
}
