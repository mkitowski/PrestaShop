<?php
namespace GetResponse\CustomFields;

use Configuration;
use GetResponse\CustomFieldsMapping\CustomFieldMapping;
use GetResponse\CustomFieldsMapping\CustomFieldMappingCollection;

class CustomFieldsRepository
{
    const RESOURCE_KEY = 'getresponse_customs';

    /**
     * @return CustomFieldMappingCollection
     */
    public function getCustomFieldsMapping()
    {
        $collection = new CustomFieldMappingCollection();

        $result = json_decode(Configuration::get(self::RESOURCE_KEY), true);

        if (empty($result)) {
            return $collection;
        }

        foreach ($result as $row) {
            $collection->add(new CustomFieldMapping(
                $row['id'],
                $row['custom_name'],
                $row['customer_property_name'],
                $row['gr_custom_id'],
                $row['is_active'],
                $row['is_default']
            ));
        }

        return $collection;
    }

    public function clearCustomFields()
    {
        Configuration::updateValue(self::RESOURCE_KEY, NULL);
    }

    /**
     * @param CustomFieldMappingCollection $collection
     * @param int|null $storeId
     */
    public function updateCustomFields(CustomFieldMappingCollection $collection, $storeId = null)
    {
        Configuration::updateValue(self::RESOURCE_KEY, json_encode($collection->toArray()), false, null, $storeId);
    }
}
