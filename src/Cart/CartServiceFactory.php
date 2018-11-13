<?php
namespace GetResponse\Cart;

use Db;
use GetResponse\Account\AccountSettings;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponse\Cache\CacheWrapper;
use GetResponse\Helper\Shop;
use GetResponseRepository;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\Cache\CacheInterface;
use GrShareCode\Api\GetresponseApiClient;
use GrShareCode\Cart\CartServiceFactory as ShareCodeCartServiceFactory;
use GrShareCode\DbRepositoryInterface;
use PrestaShopDatabaseException;

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
        $repository = new GetResponseRepository(Db::getInstance(), Shop::getUserShopId());

        return self::createCartService(
            new GetresponseApiClient(ApiFactory::createFromSettings($accountSettings), $repository),
            $repository,
            new CacheWrapper()
        );
    }

    /**
     * @return CartService
     * @throws ApiTypeException
     * @throws PrestaShopDatabaseException
     */
    public static function create()
    {
        $repository = new GetResponseRepository(Db::getInstance(), Shop::getUserShopId());
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), Shop::getUserShopId());;

        return self::createCartService(
            new GetresponseApiClient(ApiFactory::createFromSettings($accountSettingsRepository->getSettings()), $repository),
            $repository,
            new CacheWrapper()
        );

    }

    /**
     * @param GetresponseApiClient $apiClient
     * @param DbRepositoryInterface $repository
     * @param CacheInterface $cache
     * @return CartService
     */
    private static function createCartService(
        GetresponseApiClient $apiClient,
        DbRepositoryInterface $repository,
        CacheInterface $cache
    ) {
        return new CartService(
            (new ShareCodeCartServiceFactory())->create($apiClient, $repository, $cache)
        );
    }

}