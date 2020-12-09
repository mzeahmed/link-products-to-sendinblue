<?php

namespace Wc_Sendinblue_Synchronize\Admin\Woocommerce;

use Wc_Sendinblue_Synchronize\Api\Api;

class PaymentComplete
{
    public function __construct()
    {
        add_action('woocommerce_payment_complete', [$this, 'payment_complete']);
    }

    public function payment_complete($order_id)
    {
        // recuperation de la commande
        $order = wc_get_order($order_id);

        // recuperation du client
        $customer = $order->get_user();

        $contact_datas = [
            "PRENOM" => $customer->first_name,
            "NOM"    => $customer->last_name,
        ];

        // recuperation des produits achetés
        $items = $order->get_items();

        foreach ($items as $item) {
            //données du produit
            $data = $item->get_data();

            // recuperation de la liste Sendinblue liée au produit
            $postmeta = get_post_meta($data['product_id'], '_yoostart_sendinblue_list');
            $list_id  = implode('', $postmeta);

            // si la postmeta _yoostart_sendinblue_list est associé au produit
            if ($postmeta) {
                // creation de l'abonné
                Api::create_subscriber($customer->user_email, $list_id, $contact_datas);
            } else {
                // sinon on ne fait rien
                return;
            }
        }
    }
}