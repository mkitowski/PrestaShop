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
 * Class TrackingCodeServiceFactory
 * @package GetResponse\WebTracking
 */
class TrackingCodeServiceFactory
{
    /**
     * @return TrackingCodeService
     * @throws ApiTypeException
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository();
        $api = ApiFactory::createFromSettings($accountSettingsRepository->getSettings());
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new TrackingCodeService($apiClient);
    }
}
