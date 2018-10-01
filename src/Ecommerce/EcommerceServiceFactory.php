<?php
namespace GetResponse\Ecommerce;

use Db;
use GetResponse\Account\AccountServiceFactory as GrAccountServiceFactory;
use GetResponse\Account\AccountSettings;
use GetResponse\Api\ApiFactory;
use GetResponse\Helper\Shop as GrShop;
use GetResponseRepository;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\GetresponseApiClient;
use GrShareCode\Shop\ShopService;

/**
 * Class EcommerceServiceFactory
 * @package GetResponse\Ecommerce
 */
class EcommerceServiceFactory
{
    /**
     * @return EcommerceService
     * @throws ApiTypeException
     */
    public static function create()
    {
        $accountService = GrAccountServiceFactory::create();
        $settings = $accountService->getSettings();
        $api = ApiFactory::createFromSettings($settings);
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new EcommerceService(
            new EcommerceRepository(Db::getInstance(), GrShop::getUserShopId()),
            new ShopService($apiClient),
            $settings
        );
    }

    /**
     * @param AccountSettings $accountSettings
     * @return EcommerceService
     * @throws ApiTypeException
     */
    public static function createFromSettings(AccountSettings $accountSettings)
    {
        $api = ApiFactory::createFromSettings($accountSettings);
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new EcommerceService(
            new EcommerceRepository(Db::getInstance(), GrShop::getUserShopId()),
            new ShopService($apiClient),
            $accountSettings
        );
    }
}