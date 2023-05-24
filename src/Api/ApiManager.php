<?php

declare( strict_types=1 );

namespace LPTS\Api;

/**
 * Class to manage API
 *
 * @package LPTS\Api
 * @since   1.0.0
 */
class ApiManager {
	/** Transient delay time */
	public const DELAYTIME = HOUR_IN_SECONDS;

	/**
	 * Get all lists
	 *
	 * @return array|null
	 */
	public static function get_lists(): ?array {
		$account = new Api();
		$lists = $account->get_all_lists();

		$list_data = array();

		if ( $lists !== false ) {
			foreach ( $lists['lists'] as $list ) {
				$list_data[ $list['id'] ] = $list['name'];
			}
		}

		return $list_data;
	}

	/**
	 * Get all attributes
	 *
	 * @return array|null
	 */
	public static function get_attributes(): ?array {
		$attrs = get_transient( 'lpts_attributes' . get_option( LPTS_API_KEY_V3_OPTION ) );

		if ( ! $attrs ) {
			$api_client = new Api();
			$response = $api_client->get_attributes();

			$attributes = null;
			if ( $response ) {
				$attributes = $response['attributes'];
			}

			$attrs = array(
				'attributes' => array(
					'normal_attributes' => array(),
					'category_attributes' => array(),
				),
			);

			if ( $attributes !== null && count( $attributes ) > 0 ) {
				foreach ( $attributes as $key => $value ) {
					if ( $value["category"] === "normal" ) {
						$attrs['attributes']['normal_attributes'][] = $value;
					} elseif ( $value["category"] === "category" ) {
						$value["type"] = "category";
						$attrs['attributes']['category_attributes'][] = $value;
					}
				}
			}

			set_transient( 'lpts_attributes' . get_option( LPTS_API_KEY_V3_OPTION ), $attrs, self::DELAYTIME );
		}

		return $attrs;
	}

	/**
	 * Create subscriber
	 *
	 * @param string $email   Subscriber email.
	 * @param int    $list_id List id.
	 * @param array  $info    Subscriber info.
	 *
	 * @return string|void
	 * @throws \JsonException
	 */
	public static function create_subscriber( string $email, int $list_id, array $info ) {
		try {
			$api_client = new Api();

			$data = array(
				"email" => $email,
				"attributes" => $info,
				"emailBlacklisted" => false,
				"listIds" => array( $list_id ),
				"smsBlacklisted" => false,
			);

			$api_client->get_user( $email );

			if ( Api::LPTS_RESPONSE_CODE_OK === $api_client->get_last_response_code() ) {
				unset( $data["email"] );
				$api_client->update_user( $email, $data );
			} else {
				$api_client->create_user( $data );
			}

			if (
				in_array( $api_client->get_last_response_code(),
					array( Api::LPTS_RESPONSE_CODE_UPDATED, Api::LPTS_RESPONSE_CODE_CREATED ),
					true )
			) {
				return "success";
			} else {
				return "failure";
			}
		} catch ( \Exception $e ) {
			echo $e->getMessage();
		}
	}

	/**
	 * @return array|null
	 */
	public static function get_account_info(): ?array {
		$account_info = get_transient( 'lpts_client_credit_' . md5( get_option( LPTS_API_KEY_V3_OPTION ) ) );

		if ( ! $account_info ) {
			$api = new Api();
			$account = $api->get_account();

			if ( $api->get_last_response_code() === Api::LPTS_RESPONSE_CODE_OK && ! empty( $account['email'] ) ) {
				$account_email = $account['email'];

				$account_info = array(
					'account_email' => $account_email,
					'account_first_name' => $account['firstName'],
					'account_last_name' => $account['lastName'],
					'account_data' => $account['plan'],
				);
			} else {
				delete_option( LPTS_API_KEY_V3_OPTION );
			}

			set_transient(
				'lpts_client_credit_' . md5( get_option( LPTS_API_KEY_V3_OPTION ) ),
				$account_info,
				self::DELAYTIME
			);
		}

		return $account_info;
	}
}
