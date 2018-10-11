<?php
namespace GetResponse\Contact;

use GetResponse\Account\AccountSettings;
use GetResponse\Export\ExportSettings;

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
     * @param AccountSettings $settings
     * @return AddContactSettings
     */
    public static function createFromAccountSettings(AccountSettings $settings)
    {
       return new self(
           $settings->getContactListId(),
           $settings->getCycleDay(),
           $settings->isUpdateContactEnabled()
       );
    }

    /**
     * @param ExportSettings $settings
     * @return AddContactSettings
     */
    public static function createFromExportSettings(ExportSettings $settings)
    {
       return new self(
           $settings->getContactListId(),
           $settings->getCycleDay(),
           $settings->isUpdateContactInfo()
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