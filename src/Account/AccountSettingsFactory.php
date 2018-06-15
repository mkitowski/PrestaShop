<?php
namespace GetResponse\Account;

/**
 * Class AccountSettingsFactory
 * @package GetResponse\Account
 */
class AccountSettingsFactory
{
    /**
     * @param array $dbResults
     * @return AccountSettings
     */
    public static function fromDb(array $dbResults)
    {
        return new AccountSettings(
            $dbResults['id'],
            $dbResults['id_shop'],
            $dbResults['api_key'],
            $dbResults['active_subscription'],
            $dbResults['active_newsletter_subscription'],
            $dbResults['active_tracking'],
            $dbResults['tracking_snippet'],
            $dbResults['update_address'],
            $dbResults['campaign_id'],
            $dbResults['cycle_day'],
            $dbResults['account_type'],
            $dbResults['crypto']
        );
    }
}