<?php

namespace Wc_Sendinblue_Synchronize\Admin\Woocommerce;

use Wc_Sendinblue_Synchronize\Api\Api;

class PaymentComplete
{
    public array $lists;

    public function __construct()
    {
        $this->lists = Api::get_lists();

        add_action('woocommerce_payment_complete', [$this, 'payment_complete']);
    }

    public function payment_complete($order_id)
    {
        // order recovery
        $order = wc_get_order($order_id);

        // customer recovery
        $customer = $order->get_user();

        $contact_datas = [
            "PRENOM" => $customer->first_name,
            "NOM"    => $customer->last_name,
        ];

        // recovery of purchased products
        $items = $order->get_items();

        foreach ($items as $item) {
            // product datas
            $data = $item->get_data();

            // recovery of the Sendinblue list linked to the product
            $postmeta = get_post_meta($data['product_id'], '_wc_sendinblue_synchronize_list');
            $list_id  = implode('', $postmeta);

            if ($postmeta) {
                Api::create_subscriber($customer->user_email, $list_id, $contact_datas);
            }
        }
    }
}