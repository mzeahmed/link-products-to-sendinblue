<?php

declare(strict_types=1);

namespace LPTS\Domain\Services\Admin\Woocommerce;

use LPTS\Shared\Enums\MetaKey;
use LPTS\Infrastructure\External\Brevo\BrevoManager;

/**
 * @since 2.0.0
 */
class ProductColumnService
{
    public ?array $brevoLists;

    public function __construct()
    {
        $this->brevoLists = BrevoManager::getLists();
    }

    public function productsListColumn(array $columns): array
    {
        $columns['list'] = __('Brevo list', 'link-products-to-sendinblue');

        return $columns;
    }

    public function productsListColumnContent(string $column, int $product_id): void
    {
        if ('list' === $column) {
            $productListIds = get_post_meta($product_id, Metakey::PRODUCT_LIST->value, true);

            if (empty($productListIds)) {
                echo '–';

                return;
            }

            // we check if $product_list_id exist in $this->sendinblue_lists
            // if true we echo the value(list name).
            foreach ($productListIds as $row) {
                if (isset($this->brevoLists[$row['list_id']])) {
                    $list = $this->brevoLists[$row['list_id']];

                    echo esc_html($list);
                } else {
                    echo '–';
                }
            }
        }
    }
}
