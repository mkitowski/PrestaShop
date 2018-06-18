<?php
namespace GetResponse\ContactList;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GrShareCode\ContactList\ContactListService as GrContactListService;
use GrShop;

/**
 * Class ContactListServiceFactory
 * @package GetResponse\ContactList
 */
class ContactListServiceFactory
{
    /**
     * @return ContactListService
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId());
        $settings = $accountSettingsRepository->getSettings();
        $api = ApiFactory::createFromSettings($settings);

        return new ContactListService(
            new ContactListRepository(Db::getInstance(), GrShop::getUserShopId()),
            new GrContactListService($api),
            $settings
        );
    }
}