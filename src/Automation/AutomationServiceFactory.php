<?php
namespace GetResponse\Automation;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GrShareCode\Campaign\CampaignService;
use GrShop;

/**
 * Class AutomationServiceFactory
 * @package GetResponse\Automation
 */
class AutomationServiceFactory
{
    /**
     * @return AutomationService
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId());
        $settings = $accountSettingsRepository->getSettings();
        $api = ApiFactory::createFromSettings($settings);

        return new AutomationService(
            new AutomationRepository(Db::getInstance(), GrShop::getUserShopId()),
            new CampaignService($api),
            $settings
        );
    }
}