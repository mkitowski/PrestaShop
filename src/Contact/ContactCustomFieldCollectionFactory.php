<?php
namespace GetResponse\Contact;

use GetResponse\CustomFieldsMapping\CustomFieldMapping;
use GetResponse\CustomFieldsMapping\CustomFieldMappingCollection;
use GrShareCode\Contact\ContactCustomField;
use GrShareCode\Contact\ContactCustomFieldsCollection;
use GrShareCode\CustomField\CustomField;
use GrShareCode\CustomField\CustomFieldCollection;

/**
 * Class ContactCustomFieldCollectionFactory
 * @package GetResponse\Contact
 */
class ContactCustomFieldCollectionFactory
{
    /**
     * @param array $contact
     * @param CustomFieldMappingCollection $customFieldMappingCollection
     * @param CustomFieldCollection $grCustomFieldCollection
     * @param bool $updateContactInfoEnabled
     * @return ContactCustomFieldsCollection
     */
    public static function createFromContactAndCustomFieldMapping(
        $contact,
        CustomFieldMappingCollection $customFieldMappingCollection,
        CustomFieldCollection $grCustomFieldCollection,
        $updateContactInfoEnabled
    ) {
        $contactCustomFieldsCollection = new ContactCustomFieldsCollection();

        if (!$updateContactInfoEnabled) {
            return $contactCustomFieldsCollection;
        }

        $grCustomFields = self::transformCustomFieldToArray($grCustomFieldCollection);

        /** @var CustomFieldMapping $customFieldMapping */
        foreach ($customFieldMappingCollection as $customFieldMapping) {

            if (!$customFieldMapping->isActive()) {
                continue;
            }

            if (!isset($contact[$customFieldMapping->getValue()])) {
                continue;
            }

            if (!isset($grCustomFields[$customFieldMapping->getName()])) {
                continue;
            }

            $customFieldValue = $contact[$customFieldMapping->getValue()];
            $grCustomFieldId = $grCustomFields[$customFieldMapping->getName()];

            $contactCustomFieldsCollection->add(
                new ContactCustomField($grCustomFieldId, $customFieldValue)
            );
        }
    }

    /**
     * @param CustomFieldCollection $grCustomFieldCollection
     * @return array
     */
    private static function transformCustomFieldToArray(CustomFieldCollection $grCustomFieldCollection)
    {
        $customFields = [];

        /** @var CustomField $grCustomField */
        foreach ($grCustomFieldCollection as $grCustomField) {
            $customFields[$grCustomField->getName()] = $grCustomField->getId();
        }

        return $customFields;
    }
}