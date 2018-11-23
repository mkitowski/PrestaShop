<?php
namespace GetResponse\Contact;

use GetResponse\Settings\Registration\RegistrationSettings;

/**
 * Class AddContactSettings
 * @package GetResponse\Contact
 */
class AddContactSettings
{
    /** @var string */
    private $contactListId;

    /** @var string */
    private $dayOfCycle;

    /** @var bool */
    private $updateContactCustomFields;

    /**
     * @param string $contactListId
     * @param string $dayOfCycle
     * @param bool $updateContactCustomFields
     */
    public function __construct($contactListId, $dayOfCycle, $updateContactCustomFields)
    {
        $this->contactListId = $contactListId;
        $this->dayOfCycle = $dayOfCycle;
        $this->updateContactCustomFields = $updateContactCustomFields;
    }

    /**
     * @param RegistrationSettings $settings
     * @return AddContactSettings
     */
    public static function createFromConfiguration(RegistrationSettings $settings)
    {
        return new self(
            $settings->getListId(),
            $settings->getCycleDay(),
            $settings->isUpdateContactEnabled()
        );
    }

    /**
     * @return string
     */
    public function getContactListId()
    {
        return $this->contactListId;
    }

    /**
     * @return string
     */
    public function getDayOfCycle()
    {
        return $this->dayOfCycle;
    }

    /**
     * @return bool
     */
    public function isUpdateContactCustomFields()
    {
        return $this->updateContactCustomFields;
    }

}
