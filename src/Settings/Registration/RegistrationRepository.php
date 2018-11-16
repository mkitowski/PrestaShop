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
}
