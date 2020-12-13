<?php

namespace Wc_Sendinblue_Synchronize\Api;

use Exception;

class Api
{
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
                [ApiClient::RESPONSE_CODE_UPDATED, ApiClient::  RESPONSE_CODE_CREATED]
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