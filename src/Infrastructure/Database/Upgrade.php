<?php

declare(strict_types=1);

namespace LPTS\Infrastructure\Database;

use LPTS\Shared\Enums\MetaKey;
use MzeAhmed\WpToolKit\Utils\Sanitizer;

/**
 * @since 2.0.0
 */
class Upgrade
{
    private \wpdb $wpdb;

    public function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
    }

    /**
     * Methode called when an upgrade is needed
     *
     * @return void
     */
    public function dbUpgrade(): void
    {
        $this->migrateOldListMetaFormat();
    }

    /**
     * Migrate the old products list meta format to the new one
     *
     * @return void
     */
    private function migrateOldListMetaFormat()
    {
        $args = [
            'post_type' => 'product',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => Metakey::PRODUCT_LIST->value,
                    'compare' => 'EXISTS',
                ],
            ],
        ];

        $query = new \WP_Query($args);

        $posts = $query->posts;

        foreach ($posts as $post) {
            $product = wc_get_product($post->ID);

            $meta = $product->get_meta(MetaKey::PRODUCT_LIST->value);

            // Skip if already migrated (i.e. already a list of arrays)
            if (is_array($meta) && isset($meta[0]) && is_array($meta[0])) {
                continue;
            }

            $converted = [];

            // If it's a single string (one list ID)
            if (is_string($meta)) {
                $converted[] = [
                    'list_id' => Sanitizer::text($meta),
                    'condition' => 'always',
                    'param' => '',
                ];
            }

            // If it's an array of strings (multiple list IDs)
            if (is_array($meta) && !empty($meta) && is_string($meta[0])) {
                $converted = array_map(static function ($listId) {
                    return [
                        'list_id' => Sanitizer::text($listId),
                        'condition' => 'always',
                        'param' => '',
                    ];
                }, $meta);
            }

            // Only update if we actually built something
            if (!empty($converted)) {
                $product->update_meta_data(MetaKey::PRODUCT_LIST->value, $converted);
                $product->save();
            }
        }
    }
}
