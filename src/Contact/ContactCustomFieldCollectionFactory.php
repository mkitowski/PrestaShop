<?php
namespace GetResponse\Contact;

use GetResponse\CustomFieldsMapping\CustomFieldMapping;
use GetResponse\CustomFieldsMapping\CustomFieldMappingCollection;
use GrShareCode\Contact\ContactCustomField;
use GrShareCode\Contact\ContactCustomFieldsCollection;
use GrShareCode\CustomField\CustomField;
use GrShareCode\CustomField\CustomFieldCollection;
use stdClass;

/**
 * Class ContactCustomFieldCollectionFactory
 * @package GetResponse\Contact
 */
class ContactCustomFieldCollectionFactory
{
    /**
     * @param stdClass|\CustomerCore $contact
     * @param CustomFieldMappingCollection $customFieldMappingCollection
     * @param CustomFieldCollection $grCustomFieldCollection
     * @param bool $updateContactInfoEnabled
     * @return ContactCustomFieldsCollection
     */
    public function createFromContactAndCustomFieldMapping(
        $contact,
        CustomFieldMappingCollection $customFieldMappingCollection,
        CustomFieldCollection $grCustomFieldCollection,
        $updateContactInfoEnabled
    ) {
        $contactCustomFieldsCollection = new ContactCustomFieldsCollection();

        if (!$updateContactInfoEnabled) {
            return $contactCustomFieldsCollection;
        }

        $grCustomFields = $this->transformCustomFieldToArray($grCustomFieldCollection);

        /** @var CustomFieldMapping $customFieldMapping */
        foreach ($customFieldMappingCollection as $customFieldMapping) {

            if (!$customFieldMapping->isActive()) {
                continue;
            }

            $propertyKey = $customFieldMapping->getValue();
            if (!property_exists($contact, $propertyKey) || empty($contact->$propertyKey)) {
                continue;
            }

            if (!isset($grCustomFields[$customFieldMapping->getName()])) {
                continue;
            }

            $customFieldValue = $contact->$propertyKey;
            $grCustomFieldId = $grCustomFields[$customFieldMapping->getName()];

            $contactCustomFieldsCollection->add(
                new ContactCustomField($grCustomFieldId, $customFieldValue)
            );
        }

        return $contactCustomFieldsCollection;

    }

    /**
     * @param CustomFieldCollection $grCustomFieldCollection
     * @return array
     */
    private function transformCustomFieldToArray(CustomFieldCollection $grCustomFieldCollection)
    {
        $customFields = [];

        /** @var CustomField $grCustomField */
        foreach ($grCustomFieldCollection as $grCustomField) {
            $customFields[$grCustomField->getName()] = $grCustomField->getId();
        }

        return $customFields;
    }
}