<?php
namespace GetResponse\Automation;

use Db;
use GetResponse\Settings\SettingsServiceFactory;
use GrApiFactory;
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
        $settings = SettingsServiceFactory::create()->getSettings();
        $api = GrApiFactory::createFromSettings($settings);

        return new AutomationService(
            new AutomationRepository(Db::getInstance(), GrShop::getUserShopId()),
            new CampaignService($api),
            $settings
        );
    }
}