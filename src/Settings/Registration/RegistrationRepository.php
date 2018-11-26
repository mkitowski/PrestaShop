<?php

namespace GetResponse\Settings\Registration;

use Configuration;

/**
 * Class RegistrationSettings
 * @package GetResponse\WebTracking
 */
class RegistrationRepository
{
    const RESOURCE_KEY = 'getresponse_registration';

    /**
     * @return RegistrationSettings
     */
    public function getSettings()
    {
        $configuration = json_decode(Configuration::get(self::RESOURCE_KEY), true);

        if (empty($configuration)) {
            return RegistrationSettings::createEmptyInstance();
        }

        return RegistrationSettings::createFromConfiguration($configuration);
    }

    /**
     * @param RegistrationSettings $settings
     */
    public function updateSettings(RegistrationSettings $settings)
    {
        Configuration::updateValue(
            self::RESOURCE_KEY,
            json_encode([
                'active_subscription' => $settings->isActive(),
                'active_newsletter_subscription' => $settings->isNewsletterActive(),
                'campaign_id' => $settings->getListId(),
                'update_address' => $settings->isUpdateContactEnabled(),
                'cycle_day' => $settings->getCycleDay()
            ])
        );
    }

    public function clearSettings()
    {
        Configuration::updateValue(self::RESOURCE_KEY, NULL);
    }
}
