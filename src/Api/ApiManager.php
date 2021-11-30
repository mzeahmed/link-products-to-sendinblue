<?php

namespace LPTS\Api;

use Exception;

/**
 * Class to manage API
 *
 * @package LPTS\Api
 * @since   1.0.0
 */
class ApiManager
{
    /** Transient delay time */
    const DELAYTIME = HOUR_IN_SECONDS;

    public static function getLists(): array
    {
        $account = new Api();
        $lists   = $account->getAllLists();

        $list_data = [];

        if ($lists !== false) {
            foreach ($lists['lists'] as $list) {
                $list_data[$list['id']] = $list['name'];
            }
        }

        return $list_data;
    }

    /**
     * Get all attributes
     *
     * @return array|null
     */
    public static function getAttributes(): ?array
    {
        $attrs = get_transient('lpts_attributes' . md5(get_option(LPTS_API_KEY_V3_OPTION)));

        if ($attrs == false) {
            $api_client = new Api();
            $response   = $api_client->getAttributes();

            $attributes = $response['attributes'];
            $attrs      = [
                'attributes' => [
                    'normal_attributes' => [],
                    'category_attributes' => [],
                ],
            ];

            if ($attributes != null && count($attributes) > 0) {
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
                'lpts_attributes' . md5(get_option(LPTS_API_KEY_V3_OPTION)),
                $attrs,
                self::DELAYTIME
            );
        }

        return $attrs;
    }

    /**
     * Create subscriber
     *
     * @param string $email
     * @param int    $list_id
     * @param array  $info
     *
     * @return string|void
     */
    public static function createSubscriber(string $email, int $list_id, array $info)
    {
        try {
            $api_client = new Api();

            $data = [
                "email" => $email,
                "attributes" => $info,
                "emailBlacklisted" => false,
                "listIds" => [intval($list_id)],
                "smsBlacklisted" => false,
            ];

            $api_client->getUser($email);

            if (Api::LPTS_RESPONSE_CODE_OK === $api_client->getLastResponseCode()) {
                unset($data["email"]);
                $api_client->updateUser($email, $data);
            } else {
                $api_client->createUser($data);
            }

            if (in_array($api_client->getLastResponseCode(), [Api::LPTS_RESPONSE_CODE_UPDATED, Api::LPTS_RESPONSE_CODE_CREATED])
            ) {
                return "success";
            } else {
                return "failure";
            }
        } catch (Exception $e) {
        }
    }


    /**
     * @return mixed
     */
    public static function getAccountInfo(): mixed
    {
        $account_info = get_transient('lpts_client_credit_' . md5(get_option(LPTS_API_KEY_V3_OPTION)));

        if ($account_info == false) {
            $api     = new Api();
            $account = $api->getAccount();

            if ($api->getLastResponseCode() === Api::LPTS_RESPONSE_CODE_OK && ! empty($account['email'])) {
                $account_email = $account['email'];

                $account_info = [
                    'account_email' => $account_email,
                    'account_first_name' => $account['firstName'],
                    'account_last_name' => $account['lastName'],
                    'account_data' => $account['plan'],
                ];
            } else {
                delete_option(LPTS_API_KEY_V3_OPTION);
            }

            set_transient(
                'lpts_client_credit_' . md5(get_option(LPTS_API_KEY_V3_OPTION)),
                $account_info,
                self::DELAYTIME
            );
        }

        return $account_info;
    }
}
