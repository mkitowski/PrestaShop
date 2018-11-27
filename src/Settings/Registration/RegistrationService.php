<?php
namespace GetResponse\Settings\Registration;

/**
 * Class RegistrationService
 */
class RegistrationService
{
    /** @var RegistrationRepository */
    private $registrationRepository;

    /**
     * @param RegistrationRepository $registrationRepository
     */
    public function __construct(RegistrationRepository $registrationRepository)
    {
        $this->registrationRepository = $registrationRepository;
    }

    /**
     * @return RegistrationSettings
     */
    public function getSettings()
    {
        return $this->registrationRepository->getSettings();
    }

    /**
     * @param RegistrationSettings $settings
     */
    public function updateSettings(RegistrationSettings $settings)
    {
        if ($settings->isActive()) {
            $this->registrationRepository->updateSettings($settings);
        } else {
            $this->registrationRepository->clearSettings();
        }
    }

    public function clearSettings()
    {
        $this->registrationRepository->clearSettings();
    }
}
