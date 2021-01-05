<?php

namespace WcProToSL\Api;

class Api
{
    public const WCPROTOSL_API_BASE_URL = 'https://api.sendinblue.com/v3';
    public const WCPROTOSL_HTTP_METHOD_GET = 'GET';
    public const WCPROTOSL_HTTP_METHOD_POST = 'POST';
    public const WCPROTOSL_HTTP_METHOD_PUT = 'PUT';
    public const WCPROTOSL_HTTP_METHOD_DELETE = 'DELETE';
    public const WCPROTOSL_RESPONSE_CODE_OK = 200;
    public const WCPROTOSL_RESPONSE_CODE_CREATED = 201;
    public const WCPROTOSL_RESPONSE_CODE_ACCEPTED = 202;
    public const WCPROTOSL_RESPONSE_CODE_UPDATED = 204;

    public $apiKey;

    private int $lastResponseCode;

    public function __construct()
    {
        $this->apiKey = get_option(WCPROTOSL_API_KEY_V3_OPTION);
    }

    /**
     * @return mixed
     */
    public function getAccount()
    {
        return $this->get('/account');
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
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->get("/contacts/attributes");
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

        return $this->makeHttpRequest(self::WCPROTOSL_HTTP_METHOD_GET, $endpoint);
    }

    /**
     * @param         $endpoint
     * @param  array  $data
     *
     * @return mixed
     */
    public function post($endpoint, $data = [])
    {
        return $this->makeHttpRequest(self::WCPROTOSL_HTTP_METHOD_POST, $endpoint, $data);
    }

    /**
     * @param         $endpoint
     * @param  array  $data
     *
     * @return mixed
     */
    public function put($endpoint, $data = [])
    {
        return $this->makeHttpRequest(self::WCPROTOSL_HTTP_METHOD_PUT, $endpoint, $data);
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
        $url = self::WCPROTOSL_API_BASE_URL . $endpoint;

        $args = [
            'method'  => $method,
            'headers' => [
                'api-key'      => $this->apiKey,
                'Content-Type' => 'application/json',
            ],
        ];

        if ($method != self::WCPROTOSL_HTTP_METHOD_GET && $method != self::WCPROTOSL_HTTP_METHOD_DELETE) {
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
    public function getLastResponseCode(): int
    {
        return $this->lastResponseCode;
    }
}
