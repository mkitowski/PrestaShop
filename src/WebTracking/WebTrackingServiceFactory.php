<?php
namespace GetResponse\WebTracking;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GrShareCode\TrackingCode\TrackingCodeService;
use GetResponse\Helper\Shop as GrShop;

/**
 * Class WebTrackingServiceFactory
 * @package GetResponse\WebTracking
 */
class WebTrackingServiceFactory
{
    /**
     * @return WebTrackingService
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId());
        $settings = $accountSettingsRepository->getSettings();
        $api = ApiFactory::createFromSettings($settings);

        return new WebTrackingService(
            new WebTrackingRepository(Db::getInstance(), GrShop::getUserShopId()),
            new TrackingCodeService($api)
        );
    }
}