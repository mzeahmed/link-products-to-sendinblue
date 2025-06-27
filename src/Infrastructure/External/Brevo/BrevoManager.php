<?php

declare(strict_types=1);

namespace LPTS\Infrastructure\External\Brevo;

use LPTS\Shared\Utils\Utils;
use LPTS\Shared\Enums\OptionKey;
use Brevo\Client\Model\CreateModel;
use LPTS\Shared\Enums\TransientKey;
use Brevo\Client\Model\ModelInterface;

/**
 * Manages interactions with the Brevo (ex-Sendinblue) API, including contact lists,
 * folders, templates, and subscribers.
 *
 * @since 2.0.0
 */
class BrevoManager
{
    private const  DELAYTIME = HOUR_IN_SECONDS;

    /**
     * Retrieves contact attributes from Brevo API, using transient cache for performance.
     *
     * @return mixed The attributes grouped by category (normal and category).
     * @since 2.0.0
     */
    public static function getAttributes(): mixed
    {
        $attrs = get_transient(TransientKey::BREVO_ATTRIBUTES->value . md5(Utils::getApiKey()));

        if (false === $attrs) {
            $attributesResponse = self::clientInstance()->getAttributes();

            $attributes = $attributesResponse['attributes'];
            $attrs = [
                'attributes' => [
                    'normal_attributes' => [],
                    'category_attributes' => [],
                ],
            ];

            if (!empty($attributes) && count($attributes) > 0) {
                foreach ($attributes as $key => $value) {
                    if ('normal' === $value['category']) {
                        $attrs['attributes']['normal_attributes'][] = $value;
                    } elseif ('category' === $value['category']) {
                        $value['type'] = 'category';
                        $attrs['attributes']['category_attributes'][] = $value;
                    }
                }
            }

            set_transient(
                TransientKey::BREVO_ATTRIBUTES->value . md5(Utils::getApiKey()),
                $attrs,
                self::DELAYTIME
            );
        }

        return $attrs;
    }

    /**
     * Retrieves all contact lists.
     *
     * @return array|null An associative array with list ID as key and name as value.
     * @since 2.0.0
     */
    public static function getLists(): ?array
    {
        $lists = self::clientInstance()->getLists();

        $listData = [];

        foreach ($lists['lists'] as $list) {
            if ('Temp - DOUBLE OPTIN' === $list['name']) {
                continue;
            }

            $listData[$list['id']] = $list['name'];
        }

        return $listData;
    }

    /**
     * Retrieves detailed information of a specific contact list.
     *
     * @param int $listId The ID of the list.
     *
     * @return array|null Associative array with list details.
     * @since 2.0.0
     */
    public static function getList(int $listId): ?array
    {
        $list = self::clientInstance()->getList($listId);

        return [
            'id' => $list->getId(),
            'name' => $list->getName(),
            'folderId' => $list->getFolderId(),
            'totalSubscribers' => $list->getTotalSubscribers(),
            'createdAt' => $list->getCreatedAt(),
        ];
    }

    /**
     * Retrieves all contacts from a specific list.
     *
     * @param int $listId The ID of the list.
     *
     * @return array The contacts contained in the list.
     * @since 2.0.0
     */
    public static function getContactsFromList(int $listId): array
    {
        $contacts = self::clientInstance()->getContactsFromLists($listId);
        $contactsData = [];

        foreach ($contacts['contacts'] as $contact) {
            $contactsData[] = $contact;
        }

        return $contactsData;
    }

    /**
     * Creates or updates a single subscriber in a list.
     *
     * @param string $email The subscriber's email.
     * @param int $listId The target list ID.
     * @param array $attributes Optional contact attributes.
     *
     * @return string 'success' if the operation succeeded, 'failure' otherwise.
     * @since 2.0.0
     */
    public static function createSubscriber(string $email, int $listId, array $attributes = []): string
    {
        $data = [
            'email' => $email,
            'attributes' => $attributes,
            'emailBlacklisted' => false,
            'listIds' => [$listId],
            'smsBlacklisted' => false,
        ];

        $contact = self::clientInstance()->getContact($email);

        if ($contact) {
            unset($data['email']);

            $create = self::clientInstance()->updateContact($data);
        } else {
            $create = self::clientInstance()->createContact($data);
        }

        return $create ? 'success' : 'failure';
    }

    /**
     * Creates a subscriber with double opt-in (DOI).
     *
     * @param string $email The email address to register.
     * @param array $args Options including attributes, list IDs, template ID, and redirection URL.
     *
     * @return bool|string 'success', 'failure', or false on error.
     * @since 2.0.0
     */
    public static function createDoiSubscriber(string $email, array $args)
    {
        $data = [
            'email' => $email,
            'attributes' => $args['attributes'] ?? [],
            // 'emailBlacklisted' => $args['emailBlacklisted'] ?? false,
            'includeListIds' => $args['includeListIds'] ?? [],
            // 'smsBlacklisted' => $args['smsBlacklisted'] ?? false,
            'templateId' => $args['templateId'] ?? '',
            'redirectionUrl' => $args['redirectionUrl'] ?? '',
        ];

        $create = self::clientInstance()->createDoiContact($data);

        return $create ? 'success' : 'failure';
    }

    /**
     * Retrieves all folders.
     *
     * @return array
     * @since 2.0.0
     */
    public static function getFolders()
    {
        $folders = self::clientInstance()->getFolders();

        $folderData = [];

        foreach ($folders['folders'] as $folder) {
            $folderData[$folder['id']] = $folder['name'];
        }

        return $folderData;
    }

    /**
     * Retrieves lists associated with a specific folder.
     *
     * @param int $folderId
     * @param int $limit
     * @param int $offset
     * @param string $sort
     *
     * @return array|ModelInterface
     * @since 2.0.0
     */
    public static function getListsFromFolder(int $folderId, int $limit = 10, int $offset = 0)
    {
        $lists = self::clientInstance()->getFolderLists($folderId, $limit, $offset);

        $listData = [];

        foreach ($lists['lists'] as $list) {
            $listData[$list['id']] = $list['name'];
        }

        return $listData;
    }

    /**
     * Creates a new list in a given folder.
     *
     * @param int $folderId The ID of the target folder.
     * @param string $name The name of the new list.
     *
     * @return CreateModel|false The created list object or false on failure.
     * @since 2.0.0
     */
    public static function createList(int $folderId, string $name)
    {
        return self::clientInstance()->createList($folderId, $name);
    }

    /**
     * Retrieves the ID of a list by its name. Creates it if it doesn't exist.
     *
     * @param int $folderId The folder ID where to search or create the list.
     * @param string $name The name of the list.
     *
     * @return CreateModel|false|int|string The list ID, created model, or false on failure.
     * @since 2.0.0
     */
    public static function getListIdByName(int $folderId, string $name)
    {
        $folders = self::getListsFromFolder($folderId);

        if (\in_array($name, $folders, true)) {
            $listId = array_search($name, $folders, true);
        } else {
            $listId = self::createList($folderId, $name);
        }

        return $listId;
    }

    /**
     * Retrieves account information such as email and subscription plan.
     *
     * @return array|null Associative array with account data.
     * @since 2.0.0
     */
    public static function getAccountInfo(): ?array
    {
        $info = get_transient(TransientKey::BREVO_CLIENT_CREDIT->value . md5(Utils::getApiKey()));

        if (!$info) {
            $account = self::clientInstance()->getAccount();

            if (!empty($account['email'])) {
                $email = $account['email'];

                $info = [
                    'account_email' => $email,
                    'account_first_name' => $account['firstName'],
                    'account_last_name' => $account['lastName'],
                    'account_data' => $account['plan'],
                ];
            } else {
                delete_option(OptionKey::API_KEY_V3->value);
            }

            set_transient(
                TransientKey::BREVO_CLIENT_CREDIT->value . md5(Utils::getApiKey()),
                $info,
                self::DELAYTIME
            );
        }

        return $info;
    }

    /**
     * Instantiates and returns a new BrevoClient.
     *
     * @return \LPTS\Infrastructure\External\Brevo\BrevoClient The BrevoClient instance.
     * @since 2.2.0.99
     */
    private static function clientInstance()
    {
        return new BrevoClient();
    }
}
