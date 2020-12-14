<?php

namespace WcProToSL\Api;

use Exception;
use WcProToSL\Admin\Options;

class ApiManager
{
    /** Transient delay time */
    const DELAYTIME = HOUR_IN_SECONDS;

    /**
     * @return array
     */
    public static function get_lists(): array
    {
        $data = [];

        $account = new ApiClient();
        $lists   = $account->getAllLists();

        $list_data = [];

        foreach ($lists['lists'] as $list) {
            $list_data[$list['id']] = $list['name'];
        }

        $lists = $list_data;

        return $lists;
    }

    /**
     * Get all attributes
     *
     * @return \array[][]|mixed
     */
    public static function get_attributes(): array
    {
        $attrs = get_transient('wc_ss_attributes' . md5(get_option(Options::WCPROTOSL_API_KEY_V3_OPTION_NAME)));

        if ($attrs === false || $attrs == false) {
            $api_client = new ApiClient();
            $response   = $api_client->getAttributes();
            $attributes = $response['attributes'];
            $attrs      = [
                'attributes' => [
                    'normal_attributes'   => [],
                    'category_attributes' => [],
                ],
            ];

            if (count($attributes) > 0) {
                foreach ($attributes as $key => $value) {
                    if ($value["category"] == "normal") {
                        $attrs['attributes']['normal_attributes'][] = $value;
                    } elseif ($value["category"] == "category") {
                        $value["type"]                                = "category";
                        $attrs['attributes']['category_attributes'][] = $value;
                    }
                }
            }

            set_transient(
                'wc_ss_attributes' . md5(get_option(Options::WCPROTOSL_API_KEY_V3_OPTION_NAME)),
                $attrs,
                self::DELAYTIME
            );
        }

        return $attrs;
    }

    /**
     * Create subscriber
     *
     * @param  string  $email    subscriber email
     * @param  int     $list_id  list to be assigned
     * @param  array   $info     attributs(name, first-name, etc)
     *
     * @return mixed | void
     */
    public static function create_subscriber(string $email, int $list_id, array $info): string
    {
        try {
            $api_client = new ApiClient();

            $data = [
                "email"            => $email,
                "attributes"       => $info,
                "emailBlacklisted" => false,
                "listIds"          => [intval($list_id)],
                "smsBlacklisted"   => false,
            ];

            $api_client->getUser($email);

            if (ApiClient::RESPONSE_CODE_OK === $api_client->getLastResponseCode()) {
                unset($data["email"]);
                $response = $api_client->updateUser($email, $data);
            } else {
                $response = $api_client->createUser($data);
            }

            if (in_array(
                $api_client->getLastResponseCode(),
                [ApiClient::RESPONSE_CODE_UPDATED, ApiClient::RESPONSE_CODE_CREATED]
            )) {
                return "success";
            } else {
                return "failure";
            }
        } catch (Exception $e) {
        }
    }

    public static function get_account_info()
    {
        $api_client   = new ApiClient();
        $account_info = [];

        $response = $api_client->getAccount();

        if (ApiClient::RESPONSE_CODE_OK === $api_client->getLastResponseCode()) {
            $account_info['email']     = $response['email'];
            $account_info['user_name'] = $response['firstName'] . ' ' . $response['lastName'];
        }
    }
}