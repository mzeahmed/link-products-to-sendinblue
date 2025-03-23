<?php

declare(strict_types=1);

namespace LPTS\Application\Controllers\Public\Woocommerce;

use LPTS\Shared\Enums\MetaKey;
use LPTS\Shared\Enums\OptionKey;
use LPTS\Infrastructure\External\Brevo\ApiManager;
use LPTS\Application\Contract\PublicControllerInterface;

/**
 * Handles the logic for subscribing customers to Brevo lists
 *  after a WooCommerce payment is completed, based on product-specific
 *  conditions and list associations.
 *
 * @package LPTS\Admin\Woocommerce
 * @since   1.0.0
 */
class PaymentComplete implements PublicControllerInterface
{
    // private array $lists;

    /**
     * Matched customer attributes for Brevo custom fields.
     *
     * @var array|null
     */
    public ?array $clientMatchedAttributes;

    public function register(): void
    {
        $mainOption = get_option(OptionKey::MAIN_OPTION->value);
        // $this->lists = ApiManager::getLists();

        if ((false !== $mainOption) && !empty(get_option(OptionKey::API_KEY_V3->value))) {
            $this->clientMatchedAttributes = $mainOption['client_matched_attributes'];
        }

        add_action('woocommerce_payment_complete', [$this, 'paymentComplete']);
    }

    /**
     * Triggered when a WooCommerce order is marked as paid.
     * Iterates through each purchased product, retrieves the linked
     * Sendinblue lists and their conditions, and subscribes the customer
     * if the conditions are met.
     *
     * @param int $orderId Id of the order.
     *
     * @since 1.0.0
     */
    public function paymentComplete(int $orderId): void
    {
        // order recovery.
        $order = wc_get_order($orderId);
        $email = $order->get_billing_email();

        $info = [];
        if (isset($this->clientMatchedAttributes)) {
            foreach ($this->clientMatchedAttributes as $contactAttr => $customerAttr) {
                $info[$contactAttr] = $order->$customerAttr;
            }
        }

        // recovery of purchased products.
        $items = $order->get_items();

        foreach ($items as $item) {
            $productId = $item->get_product_id();
            $variationId = $item->get_variation_id();

            // $product = $item->get_product();
            // $price = $product ? (float) $product->get_price() : 0.0;

            if ($variationId) {
                // ✅ Variation : No need conditions, send to the assigned list directly.
                $variationLists = get_post_meta($variationId, Metakey::VARIATION_PRODUCT_LISTS->value, true);

                if (is_array($variationLists)) {
                    foreach ($variationLists as $listId) {
                        ApiManager::createSubscriber($email, (int) $listId, $info);
                    }
                }
            } else {
                // ✅ Simple product : Check conditions and send to the assigned list.
                $listEntries = get_post_meta($productId, Metakey::PRODUCT_LIST->value, true);

                if (is_array($listEntries)) {
                    foreach ($listEntries as $entry) {
                        $listId = $entry['list_id'] ?? null;
                        $condition = $entry['condition'] ?? 'always';
                        $param = $entry['param'] ?? '';

                        $price = $item->get_total();

                        if ($this->evaluateCondition($condition, $param, $price, $order)) {
                            ApiManager::createSubscriber($email, (int) $listId, $info);
                        }
                    }
                }
            }
        }
    }

    /**
     * Evaluates whether the given condition is met for the current order.
     *
     * Supported conditions:
     * - always: always true
     * - product_price_gte: order total greater than a given amount
     * - product_price_eq: order total equal to a given amount
     * - user_role: the user's role matches the given role
     *
     * @param string $type The condition type.
     * @param string $param The condition value/parameter.
     * @param float $price Product price.
     * @param \WC_Order $order The WooCommerce order object.
     *
     * @return bool True if the condition is met, false otherwise.
     * @since 1.0.0
     */
    private function evaluateCondition(string $type, string $param, float $price, \WC_Order $order): bool
    {
        switch ($type) {
            // case 'always':
            //     return true;
            //
            // case 'product_price_gte':
            //     return $price >= (float) $param;
            //
            // case 'product_price_eq':
            //     return abs($price - (float) $param) < 0.01;
            //
            // case 'product_price_lt':
            //     return $price < (float) $param;
            //
            // case 'user_role':
            //     $user = $order->get_user();
            //     if (!$user) {
            //         return false;
            //     }
            //
            //     return in_array($param, (array) $user->roles, true);
            //
            // default:
            //     return false;

            case 'always':
                return true;

            case 'order_total_gte':
                return $price >= (float) $param;

            case 'order_total_eq':
                return abs($price - (float) $param) < 0.01;

            case 'order_total_lt':
                return $price < (float) $param;

            // case 'user_role':
            //     $user = $order->get_user();
            //
            //     return $user && in_array($param, (array) $user->roles, true);

            default:
                return false;
        }
    }
}
