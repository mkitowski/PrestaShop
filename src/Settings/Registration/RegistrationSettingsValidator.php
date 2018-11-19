<?php
namespace GetResponse\Settings\Registration;

use Translate;

/**
 * Class RegistrationSettingsValidator
 * @package GetResponse\ContactList
 */
class RegistrationSettingsValidator
{
    /** @var array */
    private $errors;

    /** @var RegistrationSettings */
    private $registrationSettings;

    /**
     * @param RegistrationSettings $registrationSettings
     */
    public function __construct(RegistrationSettings $registrationSettings)
    {
        $this->registrationSettings = $registrationSettings;
        $this->errors = [];
        $this->validate();
    }

    private function validate()
    {
        if (empty($this->registrationSettings->getListId()) && $this->registrationSettings->isActive()) {
            $this->errors[] = Translate::getAdminTranslation('You need to select list');
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
