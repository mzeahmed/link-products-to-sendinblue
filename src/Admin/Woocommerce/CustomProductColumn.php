<?php

declare( strict_types=1 );

namespace LPTS\Admin\Woocommerce;

use LPTS\Api\ApiManager;

/**
 * Class CustomProductColumn
 * Add custom column to product list
 *
 * @package LPTS\Admin\Woocommerce
 * @since   1.0.0
 */
class CustomProductColumn {
	public ?array $sendinblue_lists;

	public function __construct() {
		$this->sendinblue_lists = ApiManager::get_lists();

		add_filter( 'manage_edit-product_columns', array( $this, 'products_list_column' ), 9999 );
		add_action( 'manage_product_posts_custom_column', array( $this, 'products_list_column_content' ), 10, 2 );
	}

	/**
	 * Create product column
	 *
	 * @param array $columns list of columns.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function products_list_column( array $columns ): array {
		$columns['list'] = __( 'Brevo list', 'link-products-to-sendinblue' );

		return $columns;
	}

	/**
	 * Add content to product column
	 *
	 * @param string $column     column name.
	 * @param int    $product_id product id.
	 *
	 * @since 1.0.0
	 */
	public function products_list_column_content( string $column, int $product_id ): void {
		if ( 'list' === $column ) {
			$product_list_id = get_post_meta( $product_id, '_lpts_list', true );

			// we check if $product_list_id exist in $this->sendinblue_lists
			// if true we echo the value(list name).
			if ( isset( $this->sendinblue_lists[ $product_list_id ] ) ) {
				$list = $this->sendinblue_lists[ $product_list_id ];

				echo esc_html( $list );
			} else {
				echo '–';
			}
		}
	}
}
