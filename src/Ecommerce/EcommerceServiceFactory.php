<?php
namespace GetResponse\Ecommerce;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponse\Settings\SettingsServiceFactory;
use GrApiFactory;
use GrShareCode\Shop\ShopService;
use GetResponse\Helper\Shop as GrShop;

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
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId());
        $settings = $accountSettingsRepository->getSettings();
        $api = ApiFactory::createFromSettings($settings);

        return new EcommerceService(
            new EcommerceRepository(Db::getInstance(), GrShop::getUserShopId()),
            new ShopService($api),
            $settings
        );
    }
}