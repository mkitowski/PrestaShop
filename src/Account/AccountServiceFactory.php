<?php
namespace GetResponse\Account;

use Db;
use GetResponse\Api\ApiFactory;
use GetResponseRepository;
use GrShareCode\Account\AccountService as GrAccountService;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\Api\Exception\GetresponseApiException;
use GrShareCode\Api\GetresponseApiClient;
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
            new AccountSettingsRepository()
        );
    }

    /**
     * @return AccountService
     * @throws GetresponseApiException
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository();
        $api = ApiFactory::createFromSettings($accountSettingsRepository->getSettings());
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new AccountService(
            new GrAccountService($apiClient),
            $accountSettingsRepository
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
            new AccountSettingsRepository()
        );
    }
}
