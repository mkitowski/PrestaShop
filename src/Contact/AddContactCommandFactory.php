<?php

namespace GetResponse\Contact;

use GetResponse\Customer\Customer;
use GetResponse\CustomFieldsMapping\CustomFieldMappingCollection;
use GrShareCode\Contact\Command\AddContactCommand;

/**
 * Class AddContactCommandFactory
 * @package GetResponse\Contact
 */
class AddContactCommandFactory
{
    /** @var CustomFieldMappingCollection */
    private $customFieldMappingCollection;

    /**
     * @param CustomFieldMappingCollection $collection
     */
    public function __construct(CustomFieldMappingCollection $collection)
    {
        $this->customFieldMappingCollection = $collection;
    }

    /**
     * @param Customer $customer
     * @param string $contactListId
     * @param int $dayOfCycle
     * @param bool $updateContactInfoEnabled
     * @return AddContactCommand
     */
    public function createFromContactAndSettings(
        Customer $customer,
        $contactListId,
        $dayOfCycle,
        $updateContactInfoEnabled
    ) {
        $contactCustomFieldCollectionFactory = new ContactCustomFieldCollectionFactory();
        $contactCustomFieldCollection = $contactCustomFieldCollectionFactory
            ->createFromContactAndCustomFieldMapping(
                $customer,
                $this->customFieldMappingCollection,
                $updateContactInfoEnabled
            );

        return new AddContactCommand(
            $customer->getEmail(),
            $customer->getName(),
            $contactListId,
            $dayOfCycle !== '' ? $dayOfCycle : null,
            $contactCustomFieldCollection
        );
    }
}
