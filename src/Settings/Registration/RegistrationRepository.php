<?php

namespace GetResponse\Settings\Registration;

use Configuration;
use ConfigurationSettings;

/**
 * Class RegistrationSettings
 * @package GetResponse\WebTracking
 */
class RegistrationRepository
{
    /**
     * @return RegistrationSettings
     */
    public function getSettings()
    {
        $configuration = json_decode(Configuration::get(ConfigurationSettings::REGISTRATION), true);

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
            ConfigurationSettings::REGISTRATION,
            json_encode([
                'active_subscription' => $settings->isActive(),
                'active_newsletter_subscription' => $settings->isNewsletterActive(),
                'campaign_id' => $settings->getListId(),
                'update_address' => $settings->isUpdateContactEnabled(),
                'cycle_day' => $settings->getCycleDay()
            ])
        );
    }
}
