<?php
namespace GetResponse\Contact;

use GetResponse\Customer\Customer;
use GetResponse\CustomFieldsMapping\CustomFieldMapping;
use GetResponse\CustomFieldsMapping\CustomFieldMappingCollection;
use GrShareCode\Contact\ContactCustomField\ContactCustomField;
use GrShareCode\Contact\ContactCustomField\ContactCustomFieldsCollection;

/**
 * Class ContactCustomFieldCollectionFactory
 * @package GetResponse\Contact
 */
class ContactCustomFieldCollectionFactory
{
    /**
     * @param Customer $customer
     * @param CustomFieldMappingCollection $customFieldMappingCollection
     * @param bool $updateContactInfoEnabled
     * @return ContactCustomFieldsCollection
     */
    public function createFromContactAndCustomFieldMapping(
        $customer,
        CustomFieldMappingCollection $customFieldMappingCollection,
        $updateContactInfoEnabled
    ) {
        $contactCustomFieldsCollection = new ContactCustomFieldsCollection();

        if (!$updateContactInfoEnabled) {
            return $contactCustomFieldsCollection;
        }

        /** @var CustomFieldMapping $customFieldMapping */
        foreach ($customFieldMappingCollection as $customFieldMapping) {

            if (!$customFieldMapping->isActive()) {
                continue;
            }

            if (empty($customer->getValueByPropertyName(
                $customFieldMapping->getCustomerPropertyName()
            ))) {
                continue;
            }

            $contactCustomFieldsCollection->add(
                new ContactCustomField(
                    $customFieldMapping->getGrCustomId(),
                    [$customer->getValueByPropertyName(
                        $customFieldMapping->getCustomerPropertyName()
                    )]
                )
            );
        }

        return $contactCustomFieldsCollection;

    }
}
