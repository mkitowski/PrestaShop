<?php
namespace GetResponse\Account;

use Configuration;
use ConfigurationSettings;

/**
 * Class AccountSettingsRepository
 * @package GetResponse\Account
 */
class AccountSettingsRepository
{
    /**
     * @return AccountSettings
     */
    public function getSettings()
    {
        $result = json_decode(Configuration::get(ConfigurationSettings::ACCOUNT), true);

        if (empty($result)) {
            return AccountSettings::createEmptyInstance();
        }

        return AccountSettings::createFromSettings($result);
    }

    /**
     * @param string $apiKey
     * @param string $accountType
     * @param string $domain
     */
    public function updateApiSettings($apiKey, $accountType, $domain)
    {
        Configuration::updateValue(
        ConfigurationSettings::ACCOUNT,
            json_encode([
                'api_key' => $apiKey,
                'type' => $accountType,
                'domain' => $domain
            ])
        );
    }

    public function clearConfiguration()
    {
        Configuration::updateValue(ConfigurationSettings::ACCOUNT, NULL);
        Configuration::updateValue(ConfigurationSettings::REGISTRATION, NULL);
        Configuration::updateValue(ConfigurationSettings::WEB_FORM, NULL);
        Configuration::updateValue(ConfigurationSettings::WEB_TRACKING, NULL);
        Configuration::updateValue(ConfigurationSettings::TRACKING_CODE, NULL);
        Configuration::updateValue(ConfigurationSettings::INVALID_REQUEST, NULL);
        Configuration::updateValue(ConfigurationSettings::ORIGIN_CUSTOM_FIELD, NULL);
    }

}
