<?php
namespace GetResponse\Ecommerce;

use Db;
use GetResponse\Settings\SettingsServiceFactory;
use GrApiFactory;
use GrShareCode\Shop\ShopService;
use GrShop;

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
        $settings = SettingsServiceFactory::create()->getSettings();
        $api = GrApiFactory::createFromSettings($settings);

        return new EcommerceService(
            new EcommerceRepository(Db::getInstance(), GrShop::getUserShopId()),
            new ShopService($api),
            $settings
        );
    }
}