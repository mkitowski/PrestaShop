<?php
namespace GetResponse\WebForm;

use Db;
use GetResponse\Settings\SettingsServiceFactory;
use GrApiFactory;
use GrShareCode\WebForm\WebFormService as GrWebFormService;
use GrShop;

/**
 * Class WebFormServiceFactory
 */
class WebFormServiceFactory
{
    /**
     * @return WebFormService
     */
    public static function create()
    {
        $settingsService = SettingsServiceFactory::create();
        $api = GrApiFactory::createFromSettings($settingsService->getSettings());

        return new WebFormService(
            new WebFormRepository(Db::getInstance(), GrShop::getUserShopId()),
            new GrWebFormService($api)
        );
    }
}