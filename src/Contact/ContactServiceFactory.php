<?php
namespace GetResponse\Contact;

use Db;
use GetResponse\Account\AccountSettings;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponse\Helper\Shop;
use GetResponseRepository;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\Contact\ContactService as GrContactService;
use GrShareCode\GetresponseApiClient;

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
        $api = ApiFactory::createFromSettings($accountSettings);
        $repository = new GetResponseRepository(Db::getInstance(), Shop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new ContactService(
            new GrContactService($apiClient)
        );
    }

    /**
     * @return ContactService
     * @throws ApiTypeException
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), Shop::getUserShopId());
        $api = ApiFactory::createFromSettings($accountSettingsRepository->getSettings());
        $repository = new GetResponseRepository(Db::getInstance(), Shop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new ContactService(
            new GrContactService($apiClient)
        );
    }
}