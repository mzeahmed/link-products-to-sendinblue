<?php

declare( strict_types=1 );

namespace LPTS\Admin\Woocommerce;

use LPTS\Api\ApiManager;

/**
 * Class PaymentComplete
 * Class for creating a subscriber when the payment is complete
 *
 * @package LPTS\Admin\Woocommerce
 * @since   1.0.0
 */
class PaymentComplete {
	public array $lists;
	public ?array $client_matched_attributes;

	public function __construct() {
		$main_option = get_option( LPTS_MAIN_OPTION );
		$this->lists = ApiManager::get_lists();

		if ( ( false !== $main_option ) && ! empty( get_option( LPTS_API_KEY_V3_OPTION ) ) ) {
			$this->client_matched_attributes = $main_option['client_matched_attributes'];
		}

		add_action( 'woocommerce_payment_complete', array( $this, 'payment_complete' ) );
	}

	/**
	 * Get WooCommrece payment data and create subscriber to Sendinblue in the list linked to the product
	 *
	 * @param int $order_id Id of the order.
	 *
	 * @throws \JsonException
	 * @since 1.0.0
	 */
	public function payment_complete( int $order_id ): void {
		// order recovery.
		$order = wc_get_order( $order_id );
		$email = $order->get_billing_email();

		$info = array();

		if ( isset( $this->client_matched_attributes ) ) {
			foreach ( $this->client_matched_attributes as $contact_attr => $customer_attr ) {
				$info[ $contact_attr ] = $order->$customer_attr;
			}
		}

		// recovery of purchased products.
		$items = $order->get_items();

		foreach ( $items as $item ) {
			// product datas.
			$data = $item->get_data();

			// recovery the Sendinblue list linked to the product.
			$postmeta = get_post_meta( $data['product_id'], '_lpts_list' );
			$list_id = implode( '', $postmeta );

			if ( $postmeta ) {
				ApiManager::create_subscriber( $email, (int) $list_id, $info );
			}
		}
	}
}
