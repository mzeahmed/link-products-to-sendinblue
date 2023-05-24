<?php

/**
 * Testing the compatibility of PHP and WordPress versions
 *
 * @param          $message
 * @param string   $subtitle
 * @param string   $title
 *
 * @since 1.0.0
 */
$errors = static function ( $message, string $subtitle = '', string $title = '' ) {
	$title = $title ? esc_html__( 'Error', 'link-products-to-sendinblue' ) : '';
	$message = "<h1>{$title}<br><small>{$subtitle}</small></h1><p>{$message}</p>";
	wp_die( $message );
};

/** Ensure PHP version compatibility */
if ( version_compare( '8.0', PHP_VERSION, '>=' ) ) {
	$errors(
		esc_html__(
			'To use Link Products to Sendinblue Lists From WooCommerce plugin, please use php 8.0 or higher',
			'link-products-to-sendinblue'
		),
		esc_html__( 'Incompatible PHP version', 'link-products-to-sendinblue' )
	);
}

/** Ensure WordPress version compatibility*/
if ( version_compare( '5.1', get_bloginfo( 'version' ), '>=' ) ) {
	$errors(
		esc_html__(
			'To use Link Products to Sendinblue Lists From WooCommerce plugin, please install WordPress 5.1 or higher',
			'link-products-to-sendinblue'
		),
		esc_html__( 'Incompatible WordPress version', 'link-products-to-sendinblue' )
	);
}
