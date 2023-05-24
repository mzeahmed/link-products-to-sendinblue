<?php

namespace LPTS\Api;

/**
 * Connecte to the Sendinblue API
 *
 * @package LPTS\Api
 * @since   1.0.0
 */
class Api {
	public const LPTS_API_BASE_URL           = 'https://api.sendinblue.com/v3';
	public const LPTS_HTTP_METHOD_GET        = 'GET';
	public const LPTS_HTTP_METHOD_POST       = 'POST';
	public const LPTS_HTTP_METHOD_PUT        = 'PUT';
	public const LPTS_HTTP_METHOD_DELETE     = 'DELETE';
	public const LPTS_RESPONSE_CODE_OK       = 200;
	public const LPTS_RESPONSE_CODE_CREATED  = 201;
	public const LPTS_RESPONSE_CODE_ACCEPTED = 202;
	public const LPTS_RESPONSE_CODE_UPDATED  = 204;

	public string $api_key;

	private $last_response_code;

	public function __construct() {
		$this->api_key = get_option( LPTS_API_KEY_V3_OPTION );
	}

	public function get_account() {
		return $this->get( '/account' );
	}

	/**
	 * @param         $endpoint
	 * @param array   $parameters
	 *
	 * @return mixed
	 */
	public function get( $endpoint, array $parameters = array() ) {
		if ( $parameters ) {
			$endpoint .= '?' . http_build_query( $parameters );
		}

		return $this->make_http_request( self::LPTS_HTTP_METHOD_GET, $endpoint );
	}

	/**
	 * @param string $method
	 * @param string $endpoint
	 * @param array  $body
	 *
	 * @return mixed
	 * @throws \JsonException
	 */
	private function make_http_request( string $method, string $endpoint, array $body = array() ) {
		$url = self::LPTS_API_BASE_URL . $endpoint;

		$args = array(
			'method' => $method,
			'headers' => array(
				'api-key' => $this->api_key,
				'Content-Type' => 'application/json',
			),
		);

		if ( $method != self::LPTS_HTTP_METHOD_GET && $method != self::LPTS_HTTP_METHOD_DELETE ) {
			if ( isset( $body['listIds'] ) ) {
				$body['listIds'] = array_map( 'intval', (array) $body['listIds'] );
			}
			$args['body'] = wp_json_encode( $body );
		}

		$response = wp_remote_request( $url, $args );
		$data = wp_remote_retrieve_body( $response );
		$this->last_response_code = wp_remote_retrieve_response_code( $response );

		return json_decode( $data, true, 512, JSON_THROW_ON_ERROR );
	}

	/**
	 * @param string $email
	 *
	 * @return mixed
	 */
	public function get_user( string $email ) {
		return $this->get( "/contacts/" . urlencode( $email ) );
	}

	/**
	 * @param array $data
	 *
	 * @return mixed
	 * @throws \JsonException
	 */
	public function create_user( array $data ) {
		return $this->post( "/contacts", $data );
	}

	/**
	 * @param         $endpoint
	 * @param array   $data
	 *
	 * @return mixed
	 * @throws \JsonException
	 */
	public function post( $endpoint, array $data = array() ) {
		return $this->make_http_request( self::LPTS_HTTP_METHOD_POST, $endpoint, $data );
	}

	/**
	 * @param string $email
	 * @param array  $data
	 *
	 * @return mixed
	 * @throws \JsonException
	 */
	public function update_user( string $email, array $data ) {
		return $this->put( "/contacts/" . $email, $data );
	}

	/**
	 * @param string $endpoint
	 * @param array  $data
	 *
	 * @return mixed
	 * @throws \JsonException
	 */
	public function put( string $endpoint, array $data = array() ) {
		return $this->make_http_request( self::LPTS_HTTP_METHOD_PUT, $endpoint, $data );
	}

	/**
	 * @return mixed
	 */
	public function get_attributes() {
		return $this->get( "/contacts/attributes" );
	}

	/**
	 * @return array|false
	 */
	public function get_all_lists() {
		if ( ! empty( $this->api_key ) ) {
			$lists = array( "lists" => array(), "count" => 0 );
			$offset = 0;
			$limit = 50;
			do {
				$list_data = $this->get_lists( array( 'limit' => $limit, 'offset' => $offset ) );
				if ( isset( $list_data["lists"] ) && is_array( $list_data["lists"] ) ) {
					$lists["lists"] = array_merge( $lists["lists"], $list_data["lists"] );
					$offset += 50;
					$lists["count"] = $list_data["count"];
				}
			} while ( ! empty( $lists['lists'] ) && count( $lists["lists"] ) < $list_data["count"] );

			return $lists;
		}

		return false;
	}

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function get_lists( array $data ) {
		return $this->get( "/contacts/lists", $data );
	}

	public function get_last_response_code() {
		return $this->last_response_code;
	}
}
