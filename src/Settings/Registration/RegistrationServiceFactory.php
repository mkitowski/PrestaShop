<?php
namespace GetResponse\Settings\Registration;

/**
 * Class RegistrationServiceFactory
 */
class RegistrationServiceFactory
{
    /**
     * @return RegistrationService
     */
    public static function createService()
    {
        return new RegistrationService(new RegistrationRepository());
    }
}
