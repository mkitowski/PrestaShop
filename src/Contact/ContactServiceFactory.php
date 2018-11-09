<?php
namespace GetResponse\Contact;

use Db;
use GetResponse\Account\AccountSettings;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponse\Helper\Shop;
use GetResponseRepository;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\Contact\ContactCustomField\ContactCustomFieldCollectionFactory as GrContactCustomFieldCollectionFactory;
use GrShareCode\Contact\ContactFactory;
use GrShareCode\Contact\ContactPayloadFactory;
use GrShareCode\Contact\ContactService as GrContactService;
use GrShareCode\CustomField\CustomFieldService;
use GrShareCode\Api\GetresponseApiClient;

/**
 * Class ContactServiceFactory
 * @package GetResponse\Contact
 */
class ContactServiceFactory
{
    /**
     * @param AccountSettings $accountSettings
     * @return ContactService
     * @throws ApiTypeException
     */
    public static function createFromSettings(AccountSettings $accountSettings)
    {
        return self::inject(
            new GetresponseApiClient(
                ApiFactory::createFromSettings($accountSettings),
                new GetResponseRepository(Db::getInstance(), Shop::getUserShopId())
            )
        );
    }

    /**
     * @return ContactService
     * @throws ApiTypeException
     * @throws \PrestaShopDatabaseException
     */
    public static function create()
    {
        return self::inject(
            new GetresponseApiClient(
                ApiFactory::createFromSettings(
                    (new AccountSettingsRepository(Db::getInstance(), Shop::getUserShopId()))->getSettings()
                ),
                new GetResponseRepository(Db::getInstance(), Shop::getUserShopId())
            )
        );
    }

    private static function inject(GetresponseApiClient $getresponseApiClient)
    {
        return new ContactService(
            new GrContactService(
                $getresponseApiClient,
                new ContactPayloadFactory(),
                new ContactFactory(new GrContactCustomFieldCollectionFactory()),
                new CustomFieldService($getresponseApiClient),
                new GetResponseRepository(Db::getInstance(), Shop::getUserShopId()),
                Contact::ORIGIN
            )
        );
    }
}