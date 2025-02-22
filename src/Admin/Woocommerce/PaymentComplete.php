<?php

declare(strict_types=1);

namespace LPTS\Admin\Woocommerce;

use LPTS\Api\ApiManager;

/**
 * Class for creating a subscriber when the payment is complete
 *
 * @package LPTS\Admin\Woocommerce
 * @since   1.0.0
 */
class PaymentComplete
{
    // private array $lists;
    public ?array $client_matched_attributes;

    public function __construct()
    {
        $mainOption = get_option(LPTS_MAIN_OPTION);
        // $this->lists = ApiManager::getLists();

        if ((false !== $mainOption) && !empty(get_option(LPTS_API_KEY_V3_OPTION))) {
            $this->client_matched_attributes = $mainOption['client_matched_attributes'];
        }

        add_action('woocommerce_payment_complete', [$this, 'paymentComplete']);
    }

    /**
     * Get WooCommrece payment data and create subscriber to Sendinblue in the list linked to the product
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
            $data = $item->get_data();

            // recovery the Sendinblue list linked to the product.
            $postmeta = get_post_meta($data['product_id'], '_lpts_list');
            $list_id = implode('', $postmeta);

            if ($postmeta) {
                ApiManager::createSubscriber($email, (int) $list_id, $info);
            }
        }
    }
}
