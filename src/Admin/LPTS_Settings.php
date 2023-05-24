<?php

declare( strict_types=1 );

namespace LPTS\Admin;

use LPTS\View\View;
use WC_Admin_Profile;
use LPTS\Api\ApiManager;

/**
 * Class Options
 *
 * @package LPTS\Admin
 * @since   1.0.0
 */
class LPTS_Settings {
	public const LPTS_API_KEY_GROUP = 'link_products_to_sendinblue';

	public ?string $nonce_action;

	public function __construct() {
		$this->nonce_action = admin_url(
			'options-general.php?page=link_products_to_sendinblue&tab=user_attributes'
		);

		add_action( 'admin_menu', [ $this, 'add_menu' ] );
		add_action( 'admin_init', [ $this, 'register_api_key_settings' ] );
		add_action( 'admin_init', [ $this, 'save_user_attributes' ] );

		add_action( 'admin_notices', [ $this, 'api_key_notice' ] );
		add_filter( 'plugin_action_links_' . LPTS_PLUGIN_BASENAME, [ $this, 'plugin_action_links' ] );

		add_action( 'admin_init', [ $this, 'main_settings' ] );
		add_action( 'admin_init', [ $this, 'delete_api_key' ] );
	}

	/**
	 * Add options menu
	 *
	 * @wp-hook admin_menu
	 * @since   1.0.0
	 */
	public function add_menu(): void {
		add_options_page(
			__( 'Link Products To Sendinblue Lists From WooCommerce settings', 'link-products-to-sendinblue' ),
			__( 'Link Products To Sendinblue Lists From WooCommerce', 'link-products-to-sendinblue' ),
			'manage_options',
			self::LPTS_API_KEY_GROUP,
			array( $this, 'form_render' )
		);
	}

	/**
	 * Initialization of api key sections
	 *
	 * @wp-hook admin_init
	 * @return void
	 * @since   1.0.0
	 */
	public function register_api_key_settings(): void {
		register_setting( self::LPTS_API_KEY_GROUP, LPTS_API_KEY_V3_OPTION );

		add_settings_section(
			'api_key_section',
			__( 'API v3 Access key', 'link-products-to-sendinblue' ),
			static function () {
				printf(
					__(
						'<p><a href="%s" target="_blank">Get your account API key</a></p>',
						'link-products-to-sendinblue'
					),
					'https://app.brevo.com/settings/keys/api'
				);
			},
			self::LPTS_API_KEY_GROUP
		);

		add_settings_field(
			'api_key_field',
			__( 'API Key', 'link-products-to-sendinblue' ),
			array( $this, 'api_key_field_render' ),
			self::LPTS_API_KEY_GROUP,
			'api_key_section'
		);
	}

	/**
	 * Save user attributes form datas
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function save_user_attributes(): void {
		/**
		 * Options
		 */
		$customer_attributes = get_option( LPTS_CUSTOMER_ATTRIBUTES_OPTION );
		$contact_attributes = get_option( LPTS_SENDINBLUE_ATTRIBUTES_OPTION );

		/**
		 * Add options if they don't exists
		 */
		false === $customer_attributes ? add_option( LPTS_CUSTOMER_ATTRIBUTES_OPTION, [] ) : null;
		false === $contact_attributes ? add_option( LPTS_SENDINBLUE_ATTRIBUTES_OPTION, [] ) : null;

		if ( isset( $_POST['_user_attributes_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_POST['_user_attributes_nonce'], $this->nonce_action ) ) {
				wp_die(
					printf(
						esc_html__(
							'Sorry, nonce <strong>%s</strong> did not verify',
							'link-products-to-sendinblue'
						),
						'_user_attributes_nonce'
					)
				);
			}

			if ( isset( $_POST['lpts_woocommerce_customer_attributes'] ) ) {
				update_option(
					LPTS_CUSTOMER_ATTRIBUTES_OPTION,
					$this->sanitize_user_attributes_form_fields( $_POST['lpts_woocommerce_customer_attributes'] )
				);
			}

			if ( isset( $_POST['lpts_sendinblue_contact_attributes'] ) ) {
				update_option(
					LPTS_SENDINBLUE_ATTRIBUTES_OPTION,
					$this->sanitize_user_attributes_form_fields( $_POST['lpts_sendinblue_contact_attributes'] )
				);
			}

			wp_safe_redirect( wp_get_referer() );
			exit();
		}
	}

	/**
	 * Sanitize datas of user attributes synch form
	 *
	 * @param array $datas Datas to sanitize.
	 *
	 * @return array|null
	 * @since 1.0.0
	 */
	private function sanitize_user_attributes_form_fields( array $datas ): ?array {
		$fields = array();

		foreach ( $datas as $k => $v ) {
			$fields[ $k ] = sanitize_text_field( $v );
		}

		return $fields;
	}

	/**
	 * Display api key fields
	 *
	 * @return string|null
	 * @since 1.0.0
	 */
	public function api_key_field_render(): ?string {
		return View::render( 'admin/options/partials/api-key-field', array(
				'api_key_v3' => get_option( LPTS_API_KEY_V3_OPTION ),
			)
		);
	}

	/**
	 * Display form
	 *
	 * @return string|null
	 * @since 1.0.0
	 */
	public function form_render(): ?string {
		$api_key = get_option( LPTS_API_KEY_V3_OPTION );
		$customer_attributes_option = get_option( LPTS_CUSTOMER_ATTRIBUTES_OPTION );
		$sendinblue_attributes_option = get_option( LPTS_SENDINBLUE_ATTRIBUTES_OPTION );

		$admin_profile = new WC_Admin_Profile();
		$customer_fields = $admin_profile->get_customer_meta_fields();
		$attrs = ApiManager::get_attributes();

		$contact_attributes = $attrs['attributes']['normal_attributes'];

		return View::render( 'admin/options/form', array(
				'api_field_group' => self::LPTS_API_KEY_GROUP,
				'api_key' => $api_key,
				'customer_attributes_option' => $customer_attributes_option,
				'sendinblue_attributes_option' => $sendinblue_attributes_option,
				'customer_fields' => $customer_fields,
				'contact_attributes' => $contact_attributes,
				'nonce_action' => $this->nonce_action,
				'matched_attributes' => $this->get_matched_attributes(),
			)
		);
	}

	/**
	 * Combine options LPTS_CUSTOMER_ATTRIBUTES_OPTION and LPTS_SENDINBLUE_ATTRIBUTES_OPTION and return result in array
	 *
	 * @return array|null Array of matched attributes
	 * @since 1.0.0
	 */
	private function get_matched_attributes(): ?array {
		return array_combine(
			get_option( LPTS_CUSTOMER_ATTRIBUTES_OPTION ),
			get_option( LPTS_SENDINBLUE_ATTRIBUTES_OPTION )
		);
	}

	/**
	 * Notice if the sendinblue API key is empty
	 *
	 * @wp-hook admin_notices
	 * @return string|null
	 * @since   1.0.0
	 */
	public function api_key_notice(): ?string {
		if ( empty( get_option( LPTS_API_KEY_V3_OPTION ) ) || ! get_option( LPTS_API_KEY_V3_OPTION ) ) {
			return View::render( 'admin/options/partials/notice', array() );
		}

		return null;
	}

	/**
	 * Add plugin settings link under plugin name on plugins page
	 *
	 * @param array $links Array of links.
	 *
	 * @wp-hook plugin_action_links_ . __FILE__
	 * @return array|null
	 * @since   1.0.0
	 */
	public function plugin_action_links( array $links ): ?array {
		$settings_links = array(
			'<a href="' . admin_url( 'options-general.php?page=link_products_to_sendinblue' ) . '">' .
			esc_html__( 'Settings', 'link-products-to-sendinblue' ) . '</a>',
		);

		return array_merge( $settings_links, $links );
	}

	/**
	 * Register main settings option
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function main_settings(): void {
		false === get_option( LPTS_MAIN_OPTION ) ? add_option( LPTS_MAIN_OPTION, array() ) : null;

		if ( ! empty( get_option( LPTS_API_KEY_V3_OPTION ) ) ) {
			$accoun_info = ApiManager::get_account_info();

			$args = array(
				'account_email' => $accoun_info['account_email'],
				'access_key' => get_option( LPTS_API_KEY_V3_OPTION ),
				'client_matched_attributes' => array_combine(
					get_option( LPTS_SENDINBLUE_ATTRIBUTES_OPTION ),
					get_option( LPTS_CUSTOMER_ATTRIBUTES_OPTION )
				),
			);

			update_option( LPTS_MAIN_OPTION, $args );
		} else {
			update_option( LPTS_MAIN_OPTION, array() );
		}
	}

	/**
	 * Delete API KEY
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function delete_api_key(): void {
		if ( isset( $_POST['lpts_delete_api_key'] ) ) {
			delete_option( LPTS_API_KEY_V3_OPTION );

			wp_safe_redirect( wp_get_referer() );
			exit();
		}
	}
}
