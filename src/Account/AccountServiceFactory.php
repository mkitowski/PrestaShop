<?php
namespace GetResponse\Account;

use GetResponse\Settings\SettingsServiceFactory;
use GrApiFactory;
use GrShareCode\Account\AccountService as GrAccountService;
use GrShareCode\TrackingCode\TrackingCodeService;

/**
 * Class AccountServiceFactory
 * @package GetResponse\Account
 */
class AccountServiceFactory
{
    /**
     * @return AccountService
     */
    public static function create()
    {
        $settings = SettingsServiceFactory::create();

        $api = GrApiFactory::createFromSettings(
            $settings->getSettings()
        );

        return new AccountService(
            new GrAccountService($api),
            $settings,
            new TrackingCodeService($api)
        );
    }

    /**
     * @return AccountService
     */
    public static function createWithSettings(array $settings)
    {
        $api = GrApiFactory::createFromArray($settings);

        return new AccountService(
            new GrAccountService($api),
            SettingsServiceFactory::create(),
            new TrackingCodeService($api)
        );
    }
}