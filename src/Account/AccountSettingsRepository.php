<?php
namespace GetResponse\Account;

use Configuration;
use GetResponse\Ecommerce\EcommerceRepository;
use GetResponse\Settings\Registration\RegistrationServiceFactory;
use GetResponse\WebForm\WebFormRepository;
use GetResponse\WebTracking\WebTrackingRepository;

/**
 * Class AccountSettingsRepository
 * @package GetResponse\Account
 */
class AccountSettingsRepository
{
    const RESOURCE_KEY = 'getresponse_account';

    /**
     * @return AccountSettings
     */
    public function getSettings()
    {
        $result = json_decode(Configuration::get(self::RESOURCE_KEY), true);

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
        self::RESOURCE_KEY,
            json_encode([
                'api_key' => $apiKey,
                'type' => $accountType,
                'domain' => $domain
            ])
        );
    }

    public function clearConfiguration()
    {
        $this->clearSettings();
        $registrationService = RegistrationServiceFactory::createService();
        $registrationService->clearSettings();
        (new WebFormRepository())->clearSettings();
        (new WebTrackingRepository())->clearWebTracking();
        (new EcommerceRepository())->clearEcommerceSettings();
        (new AccountRepository())->clearInvalidRequestDate();
        (new AccountRepository())->clearOriginCustomFieldId();
    }

    public function clearSettings()
    {
        Configuration::updateValue(self::RESOURCE_KEY, NULL);
    }
}
