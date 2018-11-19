<?php
namespace GetResponse\WebTracking;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponseRepository;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\Api\GetresponseApiClient;
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
     * @throws ApiTypeException
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository();
        $api = ApiFactory::createFromSettings($accountSettingsRepository->getSettings());
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new WebTrackingService(
            new WebTrackingRepository(),
            new TrackingCodeService($apiClient)
        );
    }
}
