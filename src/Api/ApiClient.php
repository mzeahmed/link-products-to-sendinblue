<?php

namespace Wc_Sendinblue_Synchronize\Api;

class ApiClient
{
    public const API_BASE_URL = 'https://api.sendinblue.com/v3';
    public const HTTP_METHOD_GET = 'GET';
    public const HTTP_METHOD_POST = 'POST';
    public const HTTP_METHOD_PUT = 'PUT';
    public const HTTP_METHOD_DELETE = 'DELETE';
    public const CAMPAIGN_TYPE_EMAIL = 'email';
    public const CAMPAIGN_TYPE_SMS = 'sms';
    public const RESPONSE_CODE_OK = 200;
    public const RESPONSE_CODE_CREATED = 201;
    public const RESPONSE_CODE_ACCEPTED = 202;
    public const RESPONSE_CODE_UPDATED = 204;

    /**
     * @var false|mixed|void
     */
    private $apiKey;

    private $lastResponseCode;

    public function __construct()
    {
        $this->apiKey = get_option('wc_sendinblue_synchronize_apiKey');
    }

    /**
     * @param $email
     *
     * @return mixed
     */
    public function getUser($email)
    {
        return $this->get("/contacts/" . urlencode($email));
    }

    /**
     * @param  array  $data
     *
     * @return mixed
     */
    public function createUser(array $data)
    {
        return $this->post("/contacts", $data);
    }

    /**
     * @param  string  $email
     * @param  array   $data
     *
     * @return mixed
     */
    public function updateUser(string $email, array $data)
    {
        return $this->put("/contacts/" . $email, $data);
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public function getLists($data)
    {
        return $this->get("/contacts/lists", $data);
    }

    /**
     * Recuperation de toutes les listes
     *
     * @return mixed
     */
    public function getAllLists(): array
    {
        $lists  = ["lists" => [], "count" => 0];
        $offset = 0;
        $limit  = 50;
        do {
            $list_data      = $this->getLists(['limit' => $limit, 'offset' => $offset]);
            $lists["lists"] = array_merge($lists["lists"], $list_data["lists"]);
            $offset         += 50;
        } while (count($lists["lists"]) < $list_data["count"]);
        $lists["count"] = $list_data["count"];

        return $lists;
    }

    /**
     * @param         $endpoint
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function get($endpoint, $parameters = [])
    {
        if ($parameters) {
            $endpoint .= '?' . http_build_query($parameters);
        }

        return $this->makeHttpRequest(self::HTTP_METHOD_GET, $endpoint);
    }

    /**
     * @param         $endpoint
     * @param  array  $data
     *
     * @return mixed
     */
    public function post($endpoint, $data = [])
    {
        return $this->makeHttpRequest(self::HTTP_METHOD_POST, $endpoint, $data);
    }

    /**
     * @param         $endpoint
     * @param  array  $data
     *
     * @return mixed
     */
    public function put($endpoint, $data = [])
    {
        return $this->makeHttpRequest(self::HTTP_METHOD_PUT, $endpoint, $data);
    }

    /**
     * @param         $method
     * @param         $endpoint
     * @param  array  $body
     *
     * @return mixed
     */
    private function makeHttpRequest($method, $endpoint, $body = [])
    {
        $url = self::API_BASE_URL . $endpoint;

        $args = [
            'method'  => $method,
            'headers' => [
                'api-key'      => $this->apiKey,
                'Content-Type' => 'application/json'
            ],
        ];

        if ($method != self::HTTP_METHOD_GET && $method != self::HTTP_METHOD_DELETE) {
            if (isset($body['listIds'])) {
                $body['listIds'] = array_map('intval', (array)$body['listIds']);
            }
            $args['body'] = wp_json_encode($body);
        }

        $response               = wp_remote_request($url, $args);
        $data                   = wp_remote_retrieve_body($response);
        $this->lastResponseCode = wp_remote_retrieve_response_code($response);

        return json_decode($data, true);
    }

    /**
     * @return int
     */
    public function getLastResponseCode()
    {
        return $this->lastResponseCode;
    }
}
