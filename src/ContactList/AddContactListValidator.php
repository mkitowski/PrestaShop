<?php
namespace GetResponse\ContactList;

use GrShareCode\ContactList\AddContactListCommand;
use Translate;

/**
 * Class AddContactListValidator
 * @package GetResponse\ContactList
 */
class AddContactListValidator
{
    /** @var array */
    private $errors;

    /** @var AddContactListCommand */
    private $addContactListCommand;

    /**
     * @param AddContactListCommand $addContactListCommand
     */
    public function __construct(AddContactListCommand $addContactListCommand)
    {
        $this->addContactListCommand = $addContactListCommand;
        $this->errors = [];
        $this->validate();
    }

    private function validate()
    {
        if (strlen($this->addContactListCommand->getContactListName()) < 4) {
            $this->errors[] = Translate::getAdminTranslation('The "list name" field is invalid');
        }
        if (strlen($this->addContactListCommand->getFromField()) < 4) {
            $this->errors[] = Translate::getAdminTranslation('The "from" field is required');
        }
        if (strlen($this->addContactListCommand->getReplyTo()) < 4) {
            $this->errors[] = Translate::getAdminTranslation('The "reply-to" field is required');
        }
        if (strlen($this->addContactListCommand->getSubscriptionConfirmationSubjectId()) < 4) {
            $this->errors[] = Translate::getAdminTranslation('The "confirmation subject" field is required');
        }
        if (strlen($this->addContactListCommand->getSubscriptionConfirmationBodyId()) < 4) {
            $this->errors[] = Translate::getAdminTranslation('The "confirmation body" field is required');
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