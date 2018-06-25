<?php
namespace GetResponse\Ecommerce;

use Db;
use GetResponse\Account\AccountServiceFactory as GrAccountServiceFactory;
use GrShareCode\Shop\ShopService;
use GetResponse\Helper\Shop as GrShop;
use GetResponse\Api\ApiFactory as GrApiFactory;

/**
 * Class EcommerceServiceFactory
 * @package GetResponse\Ecommerce
 */
class EcommerceServiceFactory
{
    /**
     * @return EcommerceService
     */
    public static function create()
    {
        $accountService = GrAccountServiceFactory::create();
        $settings = $accountService->getSettings();
        $api = GrApiFactory::createFromSettings($settings);

        return new EcommerceService(
            new EcommerceRepository(Db::getInstance(), GrShop::getUserShopId()),
            new ShopService($api),
            $settings
        );
    }
}