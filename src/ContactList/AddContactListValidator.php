<?php
namespace GetResponse\ContactList;

use Translate;

/**
 * Class AddContactListValidator
 * @package GetResponse\ContactList
 */
class AddContactListValidator
{
    /** @var array */
    private $errors;

    /** @var AddContactListDto */
    private $addContactListDto;

    /**
     * @param AddContactListDto $addContactListDto
     */
    public function __construct(AddContactListDto $addContactListDto)
    {
        $this->addContactListDto = $addContactListDto;
        $this->errors = [];
        $this->validate();
    }

    private function validate()
    {
        if (strlen($this->addContactListDto->getContactListName()) < 4) {
            $this->errors[] = Translate::getAdminTranslation('The "list name" field is invalid');
        }
        if (strlen($this->addContactListDto->getFromField()) < 4) {
            $this->errors[] = Translate::getAdminTranslation('The "from" field is required');
        }
        if (strlen($this->addContactListDto->getReplyTo()) < 4) {
            $this->errors[] = Translate::getAdminTranslation('The "reply-to" field is required');
        }
        if (strlen($this->addContactListDto->getSubjectId()) < 4) {
            $this->errors[] = Translate::getAdminTranslation('The "confirmation subject" field is required');
        }
        if (strlen($this->addContactListDto->getBodyId()) < 4) {
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