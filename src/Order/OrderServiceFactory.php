<?php
namespace GetResponse\Order;

use Db;
use GetResponse\Account\AccountSettings;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponse\Helper\Shop;
use GetResponse\Product\ProductFactory;
use GetResponseRepository;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\Api\GetresponseApiClient;
use GrShareCode\Order\OrderPayloadFactory;
use GrShareCode\Order\OrderService as GrOrderService;
use GrShareCode\Product\ProductService;
use PrestaShopDatabaseException;

/**
 * Class OrderServiceFactory
 * @package GetResponse\Order
 */
class OrderServiceFactory
{
    /**
     * @param AccountSettings $accountSettings
     * @return OrderService
     * @throws ApiTypeException
     */
    public static function createFromSettings(AccountSettings $accountSettings)
    {
        $repository = new GetResponseRepository(Db::getInstance(), Shop::getUserShopId());
        $apiClient = new GetresponseApiClient(ApiFactory::createFromSettings($accountSettings), $repository);

        return new OrderService(
            new GrOrderService(
                $apiClient,
                $repository,
                new ProductService($apiClient, $repository),
                new OrderPayloadFactory()
            ),
            new OrderFactory(new ProductFactory())
        );
    }

    /**
     * @return OrderService
     * @throws ApiTypeException
     * @throws PrestaShopDatabaseException
     */
    public static function create()
    {;
        $repository = new GetResponseRepository(Db::getInstance(), Shop::getUserShopId());
        $apiClient = new GetresponseApiClient(
            ApiFactory::createFromSettings(
                (new AccountSettingsRepository(Db::getInstance(), Shop::getUserShopId()))->getSettings()
            ),
            $repository
        );

        return new OrderService(
            new GrOrderService(
                $apiClient,
                $repository,
                new ProductService($apiClient, $repository),
                new OrderPayloadFactory()
            ),
            new OrderFactory(new ProductFactory())
        );
    }
}