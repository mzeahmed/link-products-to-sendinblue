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

    /**
     * Modifies the list of product columns by adding a custom column for a Brevo list.
     *
     * @param array $columns An array of existing product columns.
     *
     * @return array The modified array of product columns including the custom Brevo list column.
     */
    public function productsListColumn(array $columns)
    {
        $columns['list'] = __('Brevo list', 'link-products-to-sendinblue');

        return $columns;
    }

    /**
     * Outputs the content for a product list column based on the given column type and product ID.
     *
     * @param string $column The name of the column being processed.
     * @param int $product_id The ID of the product for which the column content is being generated.
     *
     * @return void
     */
    public function productsListColumnContent(string $column, int $product_id)
    {
        if ('list' === $column) {
            $productListIds = get_post_meta($product_id, Metakey::PRODUCT_LIST->value, true);

            if (empty($productListIds)) {
                echo '–';

                return;
            }

            $names = [];

            // we check if $product_list_id exist in $this->sendinblue_lists
            // if true we echo the value(list name).
            foreach ($productListIds as $row) {
                if (isset($this->brevoLists[$row['list_id']])) {
                    $names[] = esc_html($this->brevoLists[$row['list_id']]);
                }
            }

            if (!empty($names)) {
                echo implode(' | ', $names);
            } else {
                echo '–';
            }
        }
    }
}
