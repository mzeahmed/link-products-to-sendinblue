<?php

declare(strict_types=1);

namespace LPTS\Infrastructure\External\Brevo;

use LPTS\Constants;

/**
 * Class to manage API
 *
 * @package LPTS\Api
 * @since   1.0.0
 */
class ApiManager
{
    /** Transient delay time */
    public const DELAYTIME = HOUR_IN_SECONDS;

    /**
     * Get all lists
     *
     * @return array|null
     */
    public static function getLists(): ?array
    {
        $account = new Api();
        $lists = $account->getAllLists();

        $list_data = [];

        if (false !== $lists) {
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
        $attrs = get_transient('lpts_attributes' . get_option(Constants::LPTS_API_KEY_V3_OPTION));

        if (!$attrs) {
            $api_client = new Api();
            $response = $api_client->getAttributes();

            $attributes = null;
            if ($response) {
                $attributes = $response['attributes'];
            }

            $attrs = [
                'attributes' => [
                    'normal_attributes' => [],
                    'category_attributes' => [],
                ],
            ];

            if (null !== $attributes && count($attributes) > 0) {
                foreach ($attributes as $key => $value) {
                    if ('normal' === $value['category']) {
                        $attrs['attributes']['normal_attributes'][] = $value;
                    } elseif ('category' === $value['category']) {
                        $value['type'] = 'category';
                        $attrs['attributes']['category_attributes'][] = $value;
                    }
                }
            }

            set_transient('lpts_attributes' . get_option(Constants::LPTS_API_KEY_V3_OPTION), $attrs, self::DELAYTIME);
        }

        return $attrs;
    }

    /**
     * Create subscriber
     *
     * @param string $email Subscriber email.
     * @param int $list_id List id.
     * @param array $info Subscriber info.
     *
     * @return string|void
     */
    public static function createSubscriber(string $email, int $list_id, array $info)
    {
        try {
            $api_client = new Api();

            $data = [
                'email' => $email,
                'attributes' => $info,
                'emailBlacklisted' => false,
                'listIds' => [$list_id],
                'smsBlacklisted' => false,
            ];

            $api_client->getContact($email);

            if (Api::LPTS_RESPONSE_CODE_OK === $api_client->getLastResponseCode()) {
                unset($data['email']);
                $api_client->updateContact($email, $data);
            } else {
                $api_client->createContact($data);
            }

            if (
                \in_array(
                    $api_client->getLastResponseCode(),
                    [Api::LPTS_RESPONSE_CODE_UPDATED, Api::LPTS_RESPONSE_CODE_CREATED],
                    true
                )
            ) {
                return 'success';
            }

            return 'failure';
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return array|null
     */
    public static function getAccountInfo(): ?array
    {
        $account_info = get_transient('lpts_client_credit_' . md5(get_option(Constants::LPTS_API_KEY_V3_OPTION)));

        if (!$account_info) {
            $api = new Api();
            $account = $api->getAccount();

            if ($api->getLastResponseCode() === Api::LPTS_RESPONSE_CODE_OK && !empty($account['email'])) {
                $account_email = $account['email'];

                $account_info = [
                    'account_email' => $account_email,
                    'account_first_name' => $account['firstName'],
                    'account_last_name' => $account['lastName'],
                    'account_data' => $account['plan'],
                ];
            } else {
                delete_option(Constants::LPTS_API_KEY_V3_OPTION);
            }

            set_transient(
                'lpts_client_credit_' . md5(get_option(Constants::LPTS_API_KEY_V3_OPTION)),
                $account_info,
                self::DELAYTIME
            );
        }

        return $account_info;
    }
}
