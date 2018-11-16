<?php
namespace GetResponse\Account;

/**
 * Class AccountStatusFactory
 * @package GetResponse\Account
 */
class AccountStatusFactory
{
    /**
     * @return AccountStatus
     */
    public static function create()
    {
        return new AccountStatus(
            new AccountSettingsRepository()
        );
    }
}
