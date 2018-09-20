<?php
namespace GetResponse\WebTracking;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponseRepository;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\GetresponseApiClient;
use GrShareCode\TrackingCode\TrackingCodeService;
use GetResponse\Helper\Shop as GrShop;
use PrestaShopDatabaseException;

/**
 * Class WebTrackingServiceFactory
 * @package GetResponse\WebTracking
 */
class WebTrackingServiceFactory
{
    /**
     * @return WebTrackingService
     * @throws PrestaShopDatabaseException
     * @throws ApiTypeException
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId());
        $api = ApiFactory::createFromSettings($accountSettingsRepository->getSettings());
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new WebTrackingService(
            new WebTrackingRepository(Db::getInstance(), GrShop::getUserShopId()),
            new TrackingCodeService($apiClient)
        );
    }
}