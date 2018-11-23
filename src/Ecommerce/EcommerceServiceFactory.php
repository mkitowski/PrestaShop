<?php
namespace GetResponse\Ecommerce;

use Db;
use GetResponse\Account\AccountServiceFactory;
use GetResponse\Account\AccountSettings;
use GetResponse\Api\ApiFactory;
use GetResponse\Helper\Shop as GrShop;
use GetResponseRepository;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\Api\Exception\GetresponseApiException;
use GrShareCode\Api\GetresponseApiClient;
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
     * @throws GetresponseApiException
     */
    public static function create()
    {
        $accountSettings = AccountServiceFactory::create()->getAccountSettings();
        $api = ApiFactory::createFromSettings($accountSettings);
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new EcommerceService(
            new EcommerceRepository(),
            new ShopService($apiClient),
            $accountSettings
        );
    }
}
