<?php
namespace GetResponse\Cart;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponseRepository;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\Cart\CartService as GrCartService;
use GrShareCode\GetresponseApiClient;
use GrShareCode\Product\ProductService;
use GrShop;

/**
 * Class CartServiceFactory
 * @package GetResponse\Cart
 */
class CartServiceFactory
{
    /**
     * @return CartService
     * @throws ApiTypeException
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId());
        $api = ApiFactory::createFromSettings($accountSettingsRepository->getSettings());
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);
        $productService = new ProductService($apiClient, $repository);
        $cartService = new GrCartService($apiClient, $repository, $productService);

        return new CartService($cartService);
    }
}