<?php
namespace GetResponse\Account;

use Configuration;
use GetResponse\Config\ConfigurationKeys;

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
        $result = json_decode(Configuration::get(ConfigurationKeys::ACCOUNT), true);

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
        ConfigurationKeys::ACCOUNT,
            json_encode([
                'api_key' => $apiKey,
                'type' => $accountType,
                'domain' => $domain
            ])
        );
    }

    public function clearConfiguration()
    {
        Configuration::updateValue(ConfigurationKeys::ACCOUNT, NULL);
        Configuration::updateValue(ConfigurationKeys::REGISTRATION, NULL);
        Configuration::updateValue(ConfigurationKeys::WEB_FORM, NULL);
        Configuration::updateValue(ConfigurationKeys::WEB_TRACKING, NULL);
        Configuration::updateValue(ConfigurationKeys::TRACKING_CODE, NULL);
        Configuration::updateValue(ConfigurationKeys::ECOMMERCE, NULL);
        Configuration::updateValue(ConfigurationKeys::INVALID_REQUEST, NULL);
        Configuration::updateValue(ConfigurationKeys::ORIGIN_CUSTOM_FIELD, NULL);
    }

}
