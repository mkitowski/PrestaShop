<?php
namespace GetResponse\Settings;

use Db;
use GrShop;

/**
 * Class SettingsServiceFactory
 * @package GetResponse\Settings
 */
class SettingsServiceFactory
{
    /**
     * @return SettingsService
     */
    public static function create()
    {
        return new SettingsService(
            new SettingsRepository(Db::getInstance(), GrShop::getUserShopId())
        );
    }
}