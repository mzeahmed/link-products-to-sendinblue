<?php

declare(strict_types=1);

namespace LPTS\Infrastructure\External\Brevo;


use GuzzleHttp\Client;
use Brevo\Client\Api\ListsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Api\AccountApi;
use Brevo\Client\Model\GetLists;
use WPYoostart\Helpers\Debugger;
use LPTS\Shared\Enums\OptionKey;
use Brevo\Client\Api\ContactsApi;
use Brevo\Client\Model\GetAccount;
use Brevo\Client\Model\CreateList;
use Brevo\Client\Api\AttributesApi;
use Brevo\Client\Model\GetContacts;
use Brevo\Client\Model\CreateModel;
use Brevo\Client\Model\GetAttributes;
use Brevo\Client\Model\CreateContact;
use Brevo\Client\Model\UpdateContact;
use Brevo\Client\Model\ModelInterface;
use Brevo\Client\Model\PostContactInfo;
use Brevo\Client\Model\GetExtendedList;
use Brevo\Client\Model\CreateDoiContact;
use Brevo\Client\Model\RemoveContactFromList;
use Brevo\Client\Model\CreateUpdateContactModel;
use Brevo\Client\Model\GetExtendedContactDetails;

/**
 * Brevo API integration service.
 *
 * This class provides methods for interacting with Brevo's API,
 * including managing contacts, lists, templates, and account information.
 *
 * @since 2.0.0
 */
class BrevoClient
{
    /**
     * @var string|bool Brevo API key
     */
    private string|bool $apiKey;

    /**
     * Initializes the API key from a constant or option.
     */
    public function __construct()
    {
        $this->apiKey = \defined('LPTS_BREVO_API_KEY') ? LPTS_BREVO_API_KEY : get_option(OptionKey::API_KEY_V3->value);
    }

    /**
     * Retrieves account information from Brevo.
     *
     * @return GetAccount
     * @since 2.0.0
     */
    public function getAccount()
    {
        $data = [];

        $instance = $this->apiInstance(AccountApi::class);

        try {
            $data = $instance->getAccount();
        } catch (\Exception $e) {
            Debugger::writeLog('Exception when calling AccountApi->getAccount: ' . $e->getMessage());
        }

        return $data;
    }

    /**
     * Retrieves contact details by email.
     *
     * @param string $email
     *
     * @return GetExtendedContactDetails|null
     * @since 2.0.0
     */
    public function getContact(string $email)
    {
        $contact = null;

        $instance = $this->apiInstance(ContactsApi::class);

        try {
            $contact = $instance->getContactInfo($email);
        } catch (\Exception $e) {
            Debugger::writeLog('Exception when calling ContactsApi->getContactInfo: ' . $e->getMessage());
        }

        return $contact;
    }

    /**
     * Creates a new contact in Brevo.
     *
     * @param array $data
     *
     * @return CreateUpdateContactModel|null
     * @since 2.0.0
     */
    public function createContact(array $data)
    {
        $contact = null;

        $instance = $this->apiInstance(ContactsApi::class);

        $createContact = $this->manageContact(new CreateContact(), $data);

        try {
            $contact = $instance->createContact($createContact);
        } catch (\Exception $e) {
            Debugger::writeLog('Exception when calling ContactsApi->createContact: ' . $e->getMessage());
        }

        return $contact;
    }

    /**
     * Creates a double opt-in contact.
     *
     * @param array $data
     *
     * @return bool
     * @since 2.0.0
     */
    public function createDoiContact(array $data)
    {
        $instance = $this->apiInstance(ContactsApi::class);

        $createDoiContact = new CreateDoiContact();
        $createDoiContact->setEmail($data['email']);
        $createDoiContact->setAttributes($data['attributes']);
        $createDoiContact->setIncludeListIds($data['includeListIds']);
        $createDoiContact->setTemplateId($data['templateId']);
        $createDoiContact->setRedirectionUrl($data['redirectionUrl']);

        try {
            $instance->createDoiContact($createDoiContact);

            return true;
        } catch (\Exception $e) {
            Debugger::writeLog('Exception when calling ContactsApi->createDoiContact: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Updates an existing contact.
     *
     * @param array $data
     *
     * @return bool
     * @since 2.0.0
     */
    public function updateContact(array $data)
    {
        $instance = $this->apiInstance(ContactsApi::class);

        $updateContact = $this->manageContact(new UpdateContact(), $data);

        try {
            $instance->updateContact($updateContact, $data['email']);

            return true;
        } catch (\Exception $e) {
            Debugger::writeLog('Exception when calling ContactsApi->updateContact: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Removes one or more contacts from a list.
     *
     * @param int $listId
     * @param array|RemoveContactFromList $emails
     *
     * @return PostContactInfo|null
     * @since 2.0.0
     */
    public function removeContactFromList(int $listId, array|RemoveContactFromList $emails = [])
    {
        $config = $this->apiInstance(ContactsApi::class);

        $result = null;

        try {
            $result = $config->removeContactFromList($listId, $emails);
        } catch (\Exception $e) {
            Debugger::writeLog('Exception when calling ContactsApi->removeContactFromList: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Retrieves available attributes from Brevo.
     *
     * @return GetAttributes|array
     * @since 2.0.0
     */
    public function getAttributes()
    {
        $attributes = [];

        $instance = $this->apiInstance(AttributesApi::class);

        try {
            $attributes = $instance->getAttributes();
        } catch (\Exception $e) {
            Debugger::writeLog('Exception when calling AttributesApi->getAttributes: ' . $e->getMessage());
        }

        return $attributes;
    }

    /**
     * Retrieves paginated contacts from a specific list.
     *
     * @param int $limit
     * @param int $offset
     * @param string $sort
     * @param int|null $listId
     *
     * @return GetContacts
     * @since 2.0.0
     */
    public function getPaginatedContactsFromList(int $limit = 10, int $offset = 0, string $sort = 'desc', int $listId = null)
    {
        $contacts = [];

        $instance = $this->apiInstance(ContactsApi::class);

        try {
            $contacts = $instance->getContactsFromList(
                $listId,
                null,
                $limit,
                $offset,
                $sort
            );
        } catch (\Exception $e) {
            Debugger::writeLog('Exception when calling ContactsApi->getContactsFromList: ' . $e->getMessage());
        }

        return $contacts;
    }

    /**
     * Retrieves all contacts from a list.
     *
     * @param int $listId
     *
     * @return array
     * @since 2.0.0
     */
    public function getContactsFromLists(int $listId)
    {
        return $this->commonLists([$this, 'getPaginatedContactsFromList'], 'contacts', $listId);
    }

    /**
     * Retrieves paginated lists.
     *
     * @param int $limit
     * @param int $offset
     * @param string $sort
     *
     * @return GetLists|array
     * @since 2.0.0
     */
    public function getPaginatedLists(int $limit = 10, int $offset = 0, string $sort = 'desc')
    {
        $lists = [];

        $instance = $this->apiInstance(ListsApi::class);

        try {
            $lists = $instance->getLists($limit, $offset, $sort);
        } catch (\Exception $e) {
            Debugger::writeLog('Exception when calling ListApi->getLists: ' . $e->getMessage());
        }

        return $lists;
    }

    /**
     * Retrieves all lists.
     *
     * @return array
     * @since 2.0.0
     */
    public function getLists()
    {
        return $this->commonLists([$this, 'getPaginatedLists']);
    }

    /**
     * Retrieves detailed information for a specific list.
     *
     * @param int $listId
     *
     * @return GetExtendedList
     * @since 2.0.0
     */
    public function getList(int $listId)
    {
        $list = null;

        $instance = $this->apiInstance(ListsApi::class);

        try {
            $list = $instance->getList($listId);
        } catch (\Exception $e) {
            Debugger::writeLog('Exception when calling ListApi->getList: ' . $e->getMessage());
        }

        return $list;
    }

    /**
     * Retrieves paginated folders.
     *
     * @param int $limit
     * @param int $offset
     * @param string $sort
     *
     * @return \Brevo\Client\Model\GetFolders|array
     * @since 2.0.0
     */
    public function getPaginatedFolders(int $limit = 10, int $offset = 0, string $sort = 'desc')
    {
        $folders = [];

        $instance = $this->apiInstance(ContactsApi::class);

        try {
            $folders = $instance->getFolders($limit, $offset, $sort);
        } catch (\Exception $e) {
            Debugger::writeLog('Exception when calling ListApi->getFolders: ' . $e->getMessage());
        }

        return $folders;
    }

    /**
     * Retrieves all folders.
     *
     * @return array
     * @since 2.0.0
     */
    public function getFolders()
    {
        return $this->commonLists([$this, 'getPaginatedFolders'], 'folders');
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
    public function getFolderLists(int $folderId, int $limit = 10, int $offset = 0, string $sort = 'desc')
    {
        $lists = [];

        $instance = $this->apiInstance(ContactsApi::class);

        try {
            $lists = $instance->getFolderLists($folderId, $limit, $offset, $sort);
        } catch (\Exception $e) {
            Debugger::writeLog('Exception when calling ListApi->getListsFromFolder: ' . $e->getMessage());
        }

        return $lists;
    }

    /**
     * Creates a new contact list inside a folder.
     *
     * @param int $folderId
     * @param string $name
     *
     * @return CreateModel|false
     * @since 2.0.0
     */
    public function createList(int $folderId, string $name)
    {
        $instance = $this->apiInstance(ContactsApi::class);

        $list = new CreateList();
        $list->setFolderId($folderId);
        $list->setName($name);

        try {
            return $instance->createList($list);
        } catch (\Exception $e) {
            Debugger::writeLog('Exception when calling ListsApi->createList: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Generic method for paginated listing (used for folders and lists).
     *
     * @param callable $method
     * @param string $listingType
     * @param int|null $listId
     *
     * @return array
     * @since 2.0.0
     */
    private function commonLists(callable $method, string $listingType = 'lists', int $listId = null)
    {
        $lists = [
            $listingType => [],
            'count' => 0,
        ];

        $offset = 0;
        $limit = 50;

        do {
            $listData = $method($limit, $offset, 'desc', $listId);

            if (isset($listData[$listingType]) && \is_array($listData[$listingType])) {
                $lists[$listingType] = array_merge($lists[$listingType], $listData[$listingType]);
                $offset += 50;
                $lists['count'] = $listData['count'] ?? 0;
            }
        } while (!empty($lists[$listingType]) && count($lists[$listingType]) < $listData['count']);

        return $lists;
    }

    /**
     * Populates a CreateContact or UpdateContact object with data.
     *
     * @param CreateContact|UpdateContact $contact
     * @param array $data
     *
     * @return CreateContact|UpdateContact
     * @since 2.0.0
     */
    private function manageContact($contact, array $data)
    {
        $contact->setEmail($data['email']);
        $contact->setAttributes($data['attributes']);
        $contact->setSmsBlacklisted($data['smsBlacklisted']);
        $contact->setEmailBlacklisted($data['emailBlacklisted']);
        $contact->setListIds($data['listIds']);

        return $contact;
    }

    /**
     * Instantiates a Brevo API class with the configured API key.
     *
     * @param string $class
     *
     * @return mixed
     * @since 2.0.0
     */
    private function apiInstance(string $class)
    {
        $config = Configuration::getDefaultConfiguration();
        $config->setApiKey('api-key', $this->apiKey);

        return new $class(
            new Client(),
            $config
        );
    }
}
