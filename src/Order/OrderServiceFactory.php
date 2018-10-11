<?php
namespace GetResponse\Order;

use Db;
use GetResponse\Account\AccountSettings;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponse\Helper\Shop;
use GetResponseRepository;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\GetresponseApiClient;
use GrShareCode\Order\OrderService as GrOrderService;
use GrShareCode\Product\ProductService;
use GrShop;
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
        $api = ApiFactory::createFromSettings($accountSettings);
        $repository = new GetResponseRepository(Db::getInstance(), Shop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);
        $productService = new ProductService($apiClient, $repository);
        $orderService = new GrOrderService($apiClient, $repository, $productService);

        return new OrderService($orderService);
    }

    /**
     * @return OrderService
     * @throws ApiTypeException
     * @throws PrestaShopDatabaseException
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), Shop::getUserShopId());
        $api = ApiFactory::createFromSettings($accountSettingsRepository->getSettings());
        $repository = new GetResponseRepository(Db::getInstance(), Shop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);
        $productService = new ProductService($apiClient, $repository);
        $orderService = new GrOrderService($apiClient, $repository, $productService);

        return new OrderService($orderService);
    }
}