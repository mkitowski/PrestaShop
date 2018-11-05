<?php
namespace GetResponse\Contact;

use Customer;
use GetResponse\CustomFieldsMapping\CustomFieldMappingCollection;
use GrShareCode\Contact\Command\AddContactCommand;
use GrShareCode\CustomField\CustomFieldCollection;

/**
 * Class AddContactCommandFactory
 * @package GetResponse\Contact
 */
class AddContactCommandFactory
{
    /** @var CustomFieldMappingCollection */
    private $customFieldMappingCollection;

    /** @var CustomFieldCollection */
    private $grCustomFieldCollection;

    /**
     * @param CustomFieldMappingCollection $customFieldMappingCollection
     * @param CustomFieldCollection $customFieldCollection
     */
    public function __construct(
        CustomFieldMappingCollection $customFieldMappingCollection,
        CustomFieldCollection $customFieldCollection
    ) {
        $this->customFieldMappingCollection = $customFieldMappingCollection;
        $this->grCustomFieldCollection = $customFieldCollection;
    }

    /**
     * @param Customer $contact
     * @param string $contactListId
     * @param int $dayOfCycle
     * @param bool $updateContactInfoEnabled
     * @return AddContactCommand
     */
    public function createFromContactAndSettings(Customer $contact, $contactListId, $dayOfCycle, $updateContactInfoEnabled)
    {
        $contactCustomFieldCollectionFactory = new ContactCustomFieldCollectionFactory();
        $contactCustomFieldCollection = $contactCustomFieldCollectionFactory
            ->createFromContactAndCustomFieldMapping(
                $contact,
                $this->customFieldMappingCollection,
                $this->grCustomFieldCollection,
                $updateContactInfoEnabled
            );

        $email = $contact->email;
        $name = trim($contact->firstname . ' ' . $contact->lastname);

        return new AddContactCommand(
            $email,
            $name,
            $contactListId,
            $dayOfCycle !== '' ? $dayOfCycle : null,
            $contactCustomFieldCollection
        );
    }
}