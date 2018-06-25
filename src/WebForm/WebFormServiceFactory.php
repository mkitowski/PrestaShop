<?php
namespace GetResponse\WebForm;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GrShareCode\WebForm\WebFormService as GrWebFormService;
use GetResponse\Helper\Shop as GrShop;

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
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId());
        $settings = $accountSettingsRepository->getSettings();
        $api = ApiFactory::createFromSettings($settings);

        return new WebFormService(
            new WebFormRepository(Db::getInstance(), GrShop::getUserShopId()),
            new GrWebFormService($api)
        );
    }
}