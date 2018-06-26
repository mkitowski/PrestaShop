<?php
namespace GetResponse\Account;

use Db;
use GetResponse\Api\ApiFactory;
use GrShareCode\Account\AccountService as GrAccountService;
use GrShareCode\TrackingCode\TrackingCodeService;
use GetResponse\Helper\Shop as GrShop;

/**
 * Class AccountServiceFactory
 * @package GetResponse\Account
 */
class AccountServiceFactory
{
    /**
     * @return AccountService|null
     * @throws \PrestaShopDatabaseException
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId());
        $api = ApiFactory::createFromSettings($accountSettingsRepository->getSettings());

        return new AccountService(
            new GrAccountService($api),
            $accountSettingsRepository,
            new TrackingCodeService($api)
        );
    }

    /**
     * @param AccountDto $accountDto
     * @return AccountService
     */
    public static function createFromAccountDto(AccountDto $accountDto)
    {
        $api = ApiFactory::createFromAccountDto($accountDto);

        return new AccountService(
            new GrAccountService($api),
            new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId()),
            new TrackingCodeService($api)
        );
    }
}