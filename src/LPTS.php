<?php

declare( strict_types=1 );

namespace LPTS;

use LPTS\Admin\LPTS_Settings;
use LPTS\Admin\Woocommerce\PaymentComplete;
use LPTS\Admin\Woocommerce\CustomProductField;
use LPTS\Admin\Woocommerce\CustomProductColumn;

/**
 * @package LPTS
 * @since   1.0.0
 */
final class LPTS {
	/**
	 * Instance of LPTS
	 *
	 * @var LPTS|null $instance create only one instance from plugin primary class
	 */
	private static ?LPTS $instance = null;
	public string $request_uri;
	public array $array;

	public function __construct() {
		add_action( 'init', array( $this, 'load' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scrpts' ) );
	}

	/**
	 * Getting instance This Class is a singleton class
	 *
	 * @return LPTS|null
	 * @since 1.0.0
	 */
	public static function get_instance(): ?LPTS {
		if ( is_null( ( self::$instance ) ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Loads the necessary classes of the plugin
	 *
	 * @since 1.0.0
	 */
	public function load(): void {
		new LPTS_Settings();
		new CustomProductField();
		new PaymentComplete();
		new CustomProductColumn();

		add_action( 'init', static function () {
			load_plugin_textdomain(
				'link-products-to-sendinblue',
				false,
				LPTS_PATH . 'languages'
			);
		} );
	}

	/**
	 * Register plugin scripts and styles
	 *
	 * @return void
	 * @since 1.1.5
	 */
	public function register_scrpts(): void {
		if ( isset( $_GET['page'] ) && $_GET['page'] === 'link_products_to_sendinblue' ) {
			wp_enqueue_style(
				'lpts_bootstrap',
				LPTS_URL . 'assets/vendor/bootstrap/css/bootstrap.min.css',
				array(),
				LPTS_VERSION,
				'all'
			);

			wp_enqueue_style(
				'link_products_to_sendinblue',
				LPTS_URL . 'assets/css/app.css',
				array(),
				LPTS_VERSION,
				'all'
			);

			if ( isset( $_GET['tab'] ) && $_GET['tab'] === 'user_attributes' ) {
				wp_enqueue_script(
					'link_products_to_sendinblue',
					LPTS_URL . 'assets/js/app.js',
					array( 'jquery' ),
					LPTS_VERSION,
					true
				);
			}
		}
	}
}
