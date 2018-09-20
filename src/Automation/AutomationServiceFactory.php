<?php
namespace GetResponse\Automation;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponse\ContactList\ContactListRepository;
use GetResponse\ContactList\ContactListService;
use GetResponseRepository;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\ContactList\ContactListService as GrContactListService;
use GetResponse\Helper\Shop as GrShop;
use GrShareCode\GetresponseApiClient;

/**
 * Class AutomationServiceFactory
 * @package GetResponse\Automation
 */
class AutomationServiceFactory
{
    /**
     * @return AutomationService
     * @throws ApiTypeException
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId());
        $settings = $accountSettingsRepository->getSettings();
        $api = ApiFactory::createFromSettings($settings);
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new AutomationService(
            new AutomationRepository(Db::getInstance(), GrShop::getUserShopId()),
            new ContactListService(
                new ContactListRepository(Db::getInstance(), GrShop::getUserShopId()),
                new GrContactListService($apiClient),
                $settings
            ),
            $settings
        );
    }
}