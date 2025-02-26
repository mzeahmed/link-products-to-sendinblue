<?php

declare(strict_types=1);

namespace LPTS\Api;

/**
 * Connecte to the Sendinblue API
 *
 * @package LPTS\Api
 * @since   1.0.0
 */
class Api
{
    public const LPTS_API_BASE_URL = 'https://api.sendinblue.com/v3';
    public const LPTS_HTTP_METHOD_GET = 'GET';
    public const LPTS_HTTP_METHOD_POST = 'POST';
    public const LPTS_HTTP_METHOD_PUT = 'PUT';
    public const LPTS_HTTP_METHOD_DELETE = 'DELETE';
    public const LPTS_RESPONSE_CODE_OK = 200;
    public const LPTS_RESPONSE_CODE_CREATED = 201;
    public const LPTS_RESPONSE_CODE_ACCEPTED = 202;
    public const LPTS_RESPONSE_CODE_UPDATED = 204;

    public string $api_key;

    private $last_response_code;

    public function __construct()
    {
        $this->api_key = (string) get_option(LPTS_API_KEY_V3_OPTION) ?: '';
    }

    public function getAccount()
    {
        return $this->get('/account');
    }

    /**
     * @param         $endpoint
     * @param array $parameters
     *
     * @return mixed
     * @throws \JsonException
     */
    public function get($endpoint, array $parameters = []): mixed
    {
        if ($parameters) {
            $endpoint .= '?' . http_build_query($parameters);
        }

        return $this->makeHttpRequest(self::LPTS_HTTP_METHOD_GET, $endpoint);
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $body
     *
     * @return mixed
     * @throws \JsonException
     */
    private function makeHttpRequest(string $method, string $endpoint, array $body = []): mixed
    {
        $url = self::LPTS_API_BASE_URL . $endpoint;

        $args = [
            'method' => $method,
            'headers' => [
                'api-key' => $this->api_key,
                'Content-Type' => 'application/json',
            ],
        ];

        if ($method !== self::LPTS_HTTP_METHOD_GET && $method !== self::LPTS_HTTP_METHOD_DELETE) {
            if (isset($body['listIds'])) {
                $body['listIds'] = array_map('\intval', (array) $body['listIds']);
            }
            $args['body'] = wp_json_encode($body);
        }

        $response = wp_remote_request($url, $args);
        $data = wp_remote_retrieve_body($response);
        $this->last_response_code = wp_remote_retrieve_response_code($response);

        return !empty($data) ? json_decode($data, true, 512, JSON_THROW_ON_ERROR) : '';
    }

    /**
     * @param string $email
     *
     * @return mixed
     */
    public function getContact(string $email): mixed
    {
        return $this->get('/contacts/' . urlencode($email));
    }

    /**
     * @param array $data
     *
     * @return mixed
     * @throws \JsonException
     */
    public function createContact(array $data): mixed
    {
        return $this->post('/contacts', $data);
    }

    /**
     * @param         $endpoint
     * @param array $data
     *
     * @return mixed
     * @throws \JsonException
     */
    public function post($endpoint, array $data = [])
    {
        return $this->makeHttpRequest(self::LPTS_HTTP_METHOD_POST, $endpoint, $data);
    }

    /**
     * @param string $email
     * @param array $data
     *
     * @return mixed
     * @throws \JsonException
     */
    public function updateContact(string $email, array $data)
    {
        return $this->put('/contacts/' . $email, $data);
    }

    /**
     * @param string $endpoint
     * @param array $data
     *
     * @return mixed
     * @throws \JsonException
     */
    public function put(string $endpoint, array $data = [])
    {
        return $this->makeHttpRequest(self::LPTS_HTTP_METHOD_PUT, $endpoint, $data);
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->get('/contacts/attributes');
    }

    /**
     * @return array|false
     */
    public function getAllLists(): false|array
    {
        if (!empty($this->api_key)) {
            $lists = ['lists' => [], 'count' => 0];
            $offset = 0;
            $limit = 50;
            do {
                $list_data = $this->getLists(['limit' => $limit, 'offset' => $offset]);
                if (isset($list_data['lists']) && \is_array($list_data['lists'])) {
                    $lists['lists'] = array_merge($lists['lists'], $list_data['lists']);
                    $offset += 50;
                    $lists['count'] = $list_data['count'];
                }
            } while (!empty($lists['lists']) && count($lists['lists']) < $list_data['count']);

            return $lists;
        }

        return false;
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function getLists(array $data): mixed
    {
        return $this->get('/contacts/lists', $data);
    }

    public function getLastResponseCode()
    {
        return $this->last_response_code;
    }
}
