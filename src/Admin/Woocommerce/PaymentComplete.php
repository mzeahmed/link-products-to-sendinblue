<?php

declare(strict_types=1);

namespace LPTS\Admin\Woocommerce;

use LPTS\Constants;
use LPTS\Api\ApiManager;

/**
 * Handles the logic for subscribing customers to Brevo lists
 *  after a WooCommerce payment is completed, based on product-specific
 *  conditions and list associations.
 *
 * @package LPTS\Admin\Woocommerce
 * @since   1.0.0
 */
class PaymentComplete
{
    // private array $lists;

    /**
     * Matched customer attributes for Brevo custom fields.
     *
     * @var array|null
     */
    public ?array $client_matched_attributes;

    public function __construct()
    {
        $mainOption = get_option(Constants::LPTS_MAIN_OPTION);
        // $this->lists = ApiManager::getLists();

        if ((false !== $mainOption) && !empty(get_option(Constants::LPTS_API_KEY_V3_OPTION))) {
            $this->client_matched_attributes = $mainOption['client_matched_attributes'];
        }

        add_action('woocommerce_payment_complete', [$this, 'paymentComplete']);
    }

    /**
     * Triggered when a WooCommerce order is marked as paid.
     * Iterates through each purchased product, retrieves the linked
     * Sendinblue lists and their conditions, and subscribes the customer
     * if the conditions are met.
     *
     * @param int $order_id Id of the order.
     *
     * @since 1.0.0
     */
    public function paymentComplete(int $order_id): void
    {
        // order recovery.
        $order = wc_get_order($order_id);
        $email = $order->get_billing_email();

        $info = [];

        if (isset($this->client_matched_attributes)) {
            foreach ($this->client_matched_attributes as $contactAttr => $customerAttr) {
                $info[$contactAttr] = $order->$customerAttr;
            }
        }

        // recovery of purchased products.
        $items = $order->get_items();

        foreach ($items as $item) {
            // product datas.
            // $data = $item->get_data();
            //
            // // recovery the Sendinblue list linked to the product.
            // $postmeta = get_post_meta($data['product_id'], '_lpts_list');
            // $list_id = implode('', $postmeta);
            //
            // if ($postmeta) {
            //     ApiManager::createSubscriber($email, (int) $list_id, $info);
            // }

            $product_id = $item->get_product_id();
            $list_entries = get_post_meta($product_id, '_lpts_list', true);

            if (is_array($list_entries)) {
                foreach ($list_entries as $entry) {
                    $listId = $entry['list_id'] ?? null;
                    $condition = $entry['condition'] ?? 'always';
                    $param = $entry['param'] ?? '';

                    if ($this->evaluateCondition($condition, $param, $order)) {
                        ApiManager::createSubscriber($email, (int) $listId, $info);
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
     * - order_total_gt: order total greater than a given amount
     * - order_total_eq: order total equal to a given amount
     * - user_role: the user's role matches the given role
     *
     * @param string $type The condition type.
     * @param string $param The condition value/parameter.
     * @param \WC_Order $order The WooCommerce order object.
     *
     * @return bool True if the condition is met, false otherwise.
     * @since 1.0.0
     */
    private function evaluateCondition(string $type, string $param, \WC_Order $order): bool
    {
        switch ($type) {
            case 'always':
                return true;

            case 'order_total_gt':
                return (float) $order->get_total() > (float) $param;

            case 'order_total_eq':
                return abs((float) $order->get_total() - (float) $param) < 0.01;

            case 'user_role':
                $user = $order->get_user();

                if (!$user) {
                    return false;
                }

                return in_array($param, (array) $user->roles, true);

            default:
                return false;
        }
    }
}
