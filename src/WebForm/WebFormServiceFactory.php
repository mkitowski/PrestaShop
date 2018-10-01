<?php
namespace GetResponse\WebForm;

use Db;
use GetResponse\Account\AccountSettings;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponseRepository;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\GetresponseApiClient;
use GrShareCode\WebForm\WebFormService as GrWebFormService;
use GetResponse\Helper\Shop as GrShop;
use PrestaShopDatabaseException;

/**
 * Class WebFormServiceFactory
 */
class WebFormServiceFactory
{
    /**
     * @param AccountSettings $accountSettings
     * @return WebFormService
     * @throws ApiTypeException
     */
    public static function createFromSettings(AccountSettings $accountSettings)
    {
        $api = ApiFactory::createFromSettings($accountSettings);
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new WebFormService(
            new WebFormRepository(Db::getInstance(), GrShop::getUserShopId()),
            new GrWebFormService($apiClient)
        );
    }

    /**
     * @return WebFormService
     * @throws ApiTypeException
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId());
        $settings = $accountSettingsRepository->getSettings();
        $api = ApiFactory::createFromSettings($settings);
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new WebFormService(
            new WebFormRepository(Db::getInstance(), GrShop::getUserShopId()),
            new GrWebFormService($apiClient)
        );
    }
}