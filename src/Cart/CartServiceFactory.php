<?php
namespace GetResponse\Cart;

use Db;
use GetResponse\Account\AccountSettings;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponse\Helper\Shop;
use GetResponseRepository;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\Cart\CartService as GrCartService;
use GrShareCode\GetresponseApiClient;
use GrShareCode\Product\ProductService;

/**
 * Class CartServiceFactory
 * @package GetResponse\Cart
 */
class CartServiceFactory
{
    /**
     * @param AccountSettings $accountSettings
     * @return CartService
     * @throws ApiTypeException
     */
    public static function createFromAccountSettings(AccountSettings $accountSettings)
    {
        $api = ApiFactory::createFromSettings($accountSettings);
        $repository = new GetResponseRepository(Db::getInstance(), Shop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);
        $productService = new ProductService($apiClient, $repository);
        $cartService = new GrCartService($apiClient, $repository, $productService);

        return new CartService($cartService);
    }

    /**
     * @return CartService
     * @throws ApiTypeException
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), Shop::getUserShopId());
        $api = ApiFactory::createFromSettings($accountSettingsRepository->getSettings());
        $repository = new GetResponseRepository(Db::getInstance(), Shop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);
        $productService = new ProductService($apiClient, $repository);
        $cartService = new GrCartService($apiClient, $repository, $productService);

        return new CartService($cartService);
    }
}