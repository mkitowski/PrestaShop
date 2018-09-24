<?php
namespace GetResponse\Order;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponseRepository;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\GetresponseApiClient;
use GrShareCode\Order\OrderService as GrOrderService;
use GrShareCode\Product\ProductService;
use GrShop;

/**
 * Class OrderServiceFactory
 * @package GetResponse\Order
 */
class OrderServiceFactory
{
    /**
     * @return OrderService
     * @throws ApiTypeException
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId());
        $api = ApiFactory::createFromSettings($accountSettingsRepository->getSettings());
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);
        $productService = new ProductService($apiClient, $repository);
        $orderService = new GrOrderService($apiClient, $repository, $productService);

        return new OrderService($orderService);
    }
}