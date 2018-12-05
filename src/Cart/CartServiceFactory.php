<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author     Getresponse <grintegrations@getresponse.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace GetResponse\Cart;

use Db;
use GetResponse\Account\AccountSettings;
use GetResponse\Api\ApiFactory;
use GetResponse\Cache\CacheWrapper;
use GetResponse\Helper\Shop;
use GetResponseRepository;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\Cache\CacheInterface;
use GrShareCode\Api\GetresponseApiClient;
use GrShareCode\Cart\CartServiceFactory as ShareCodeCartServiceFactory;
use GrShareCode\DbRepositoryInterface;

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
            (new ShareCodeCartServiceFactory())->create($apiClient, $repository, $cache),
            _PS_BASE_URL_.__PS_BASE_URI__
        );
    }
}
