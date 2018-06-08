<?php
namespace GetResponse\Settings;

/**
 * Class SettingsFactory
 * @package GetResponse\Settings
 */
class SettingsFactory
{
    /**
     * @param array $dbResults
     * @return Settings
     */
    public static function fromDb(array $dbResults)
    {
        return new Settings(
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