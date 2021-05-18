<?php

namespace LPTS\Admin\Woocommerce;

use LPTS\Api\ApiManager;

/**
 * Class PaymentComplete
 *
 * @package LPTS\Admin\Woocommerce
 * @since   1.0.0
 */
class PaymentComplete
{
    public $lists;
    public $client_matched_attributes;

    public function __construct()
    {
        $main_option = get_option(LPTS_MAIN_OPTION);
        $this->lists = ApiManager::get_lists();

        if (! empty(get_option(LPTS_API_KEY_V3_OPTION))) {
            $this->client_matched_attributes = $main_option['client_matched_attributes'];
        }

        add_action('woocommerce_payment_complete', [$this, 'paymentComplete']);
    }

    /**
     * @param $order_id
     *
     * @since 1.0.0
     */
    public function paymentComplete($order_id)
    {
        // order recovery
        $order = wc_get_order($order_id);
        $email = $order->get_billing_email();

        $info = [];

        if (isset($this->client_matched_attributes)) {
            foreach ($this->client_matched_attributes as $contact_attr => $customer_attr) {
                $info[$contact_attr] = $order->$customer_attr;
            }
        }

        // recovery of purchased products
        $items = $order->get_items();

        foreach ($items as $item) {
            // product datas
            $data = $item->get_data();

            // recovery the Sendinblue list linked to the product
            $postmeta = get_post_meta($data['product_id'], '_lpts_list');
            $list_id  = implode('', $postmeta);

            if ($postmeta) {
                ApiManager::create_subscriber($email, $list_id, $info);
            }
        }
    }
}
