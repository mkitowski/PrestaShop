<?php
namespace GetResponse\ContactList;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponse\Helper\Shop as GrShop;
use GetResponseRepository;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\ContactList\ContactListService as GrContactListService;
use GrShareCode\GetresponseApiClient;

/**
 * Class ContactListServiceFactory
 * @package GetResponse\ContactList
 */
class ContactListServiceFactory
{
    /**
     * @return ContactListService
     * @throws ApiTypeException
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId());
        $settings = $accountSettingsRepository->getSettings();
        $api = ApiFactory::createFromSettings($settings);
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new ContactListService(
            new ContactListRepository(Db::getInstance(), GrShop::getUserShopId()),
            new GrContactListService($apiClient),
            $settings
        );
    }
}