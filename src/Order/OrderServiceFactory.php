<?php
namespace GetResponse\Order;

use Db;
use GetResponse\Account\AccountSettings;
use GetResponse\Api\ApiFactory;
use GetResponse\Helper\Shop;
use GetResponse\Product\ProductFactory;
use GetResponseRepository;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\Api\GetresponseApiClient;
use GrShareCode\DbRepositoryInterface;
use GrShareCode\Order\OrderServiceFactory as ShareCodeOrderServiceFactory;

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

        return self::createOrderService($apiClient, $repository);
    }

    private static function createOrderService(GetresponseApiClient $apiClient, DbRepositoryInterface $repository)
    {
        return new OrderService(
            (new ShareCodeOrderServiceFactory())->create($apiClient, $repository),
            new OrderFactory(new ProductFactory())
        );
    }
}
