<?php
namespace GetResponse\CustomFieldsMapping;

use GrShareCode\TypedCollection;

/**
 * Class CustomFieldMappingCollection
 * @package GetResponse\CustomFieldsMapping
 */
class CustomFieldMappingCollection extends TypedCollection
{
    public function __construct()
    {
        $this->setItemType('\GetResponse\CustomFieldsMapping\CustomFieldMapping');
    }
}