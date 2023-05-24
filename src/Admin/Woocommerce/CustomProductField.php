<?php

declare( strict_types=1 );

namespace LPTS\Admin\Woocommerce;

use LPTS\View\View;
use LPTS\Api\ApiManager;

/**
 * Class CustomProductField
 * Add a custom field to the product page
 *
 * @package LPTS\Admin\Woocommerce
 * @since   1.0.0
 */
class CustomProductField {
	public ?array $lists;
	public string|bool $api_key;

	public function __construct() {
		$this->lists = ApiManager::get_lists();
		$this->api_key = get_option( LPTS_API_KEY_V3_OPTION );

		// push 'Select a list' to $this->list.
		$this->lists[] = __( 'Select a list', 'link-products-to-sendinblue' );

		// we sort sendinblue list by key(id) in reverse order, to add 'Select a list' as first element of the array.
		krsort( $this->lists );

		if ( ! empty( $this->api_key ) ) {
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'custom_product_data_tab' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'product_data_panel_render' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'process_product_meta' ) );
		}
	}

	/**
	 * Add new tabs
	 *
	 * @param array $tabs list of tabs
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function custom_product_data_tab( array $tabs ): array {
		$tabs['sendinblue'] = array(
			'label' => __( 'Brevo (ex Sendinblue)', 'link-products-to-sendinblue' ),
			'target' => 'sendinblue_data_panel',
			'class' => array( 'hide_if_external' ),
			'priority' => 100,
		);

		return $tabs;
	}

	/**
	 * Display tab view
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function product_data_panel_render(): string {
		$value = get_post_meta( get_the_ID(), '_lpts_list' ) ?: '';

		return View::render( 'admin/woocommerce/product-sendinblue-panel',
			array(
				'lists' => $this->lists,
				'value' => $value,
			)
		);
	}

	/**
	 * Saving field in the database
	 *
	 * @param int $post_id Id of product.
	 *
	 * @since 1.0.0
	 */
	public function process_product_meta( int $post_id ): void {
		$product = wc_get_product( $post_id );
		$product->update_meta_data( '_lpts_list', sanitize_text_field( $_POST['_selec_list'] ) );
		$product->save();
	}
}
