<?php
namespace GetResponse\ContactList;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponse\Helper\Shop as GrShop;
use GetResponseRepository;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\ContactList\ContactListService as GrContactListService;
use GrShareCode\Api\GetresponseApiClient;

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
        $accountSettingsRepository = new AccountSettingsRepository();
        $settings = $accountSettingsRepository->getSettings();
        $api = ApiFactory::createFromSettings($settings);
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new ContactListService(new GrContactListService($apiClient));
    }
}
