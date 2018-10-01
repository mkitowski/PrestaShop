<?php
namespace GetResponse\Account;

use Db;
use GetResponse\Api\ApiFactory;
use GetResponseRepository;
use GrShareCode\Account\AccountService as GrAccountService;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\GetresponseApiClient;
use GrShareCode\TrackingCode\TrackingCodeService;
use GetResponse\Helper\Shop as GrShop;

/**
 * Class AccountServiceFactory
 * @package GetResponse\Account
 */
class AccountServiceFactory
{
    /**
     * @param AccountSettings $accountSettings
     * @return AccountService
     * @throws ApiTypeException
     */
    public static function createFromAccountSettings(AccountSettings $accountSettings)
    {
        $api = ApiFactory::createFromSettings($accountSettings);
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new AccountService(
            new GrAccountService($apiClient),
            new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId()),
            new TrackingCodeService($apiClient)
        );
    }

    /**
     * @return AccountService
     * @throws ApiTypeException
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId());
        $api = ApiFactory::createFromSettings($accountSettingsRepository->getSettings());
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new AccountService(
            new GrAccountService($apiClient),
            $accountSettingsRepository,
            new TrackingCodeService($apiClient)
        );
    }

    /**
     * @param AccountDto $accountDto
     * @return AccountService
     * @throws ApiTypeException
     */
    public static function createFromAccountDto(AccountDto $accountDto)
    {
        $api = ApiFactory::createFromAccountDto($accountDto);
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new AccountService(
            new GrAccountService($apiClient),
            new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId()),
            new TrackingCodeService($apiClient)
        );
    }
}