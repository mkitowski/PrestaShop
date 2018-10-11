<?php
namespace GetResponse\CustomFieldsMapping;

use Exception;

/**
 * Class CustomFieldMappingException
 * @package GetResponse\CustomFieldsMapping
 */
class CustomFieldMappingException extends Exception
{
    /**
     * @param int $customFieldMappingId
     * @return CustomFieldMappingException
     */
    public static function createForNotFoundCustomFieldMapping($customFieldMappingId)
    {
        return new self(sprintf('Custom field mapping not found with id: %s.', $customFieldMappingId));
    }

    /**
     * @param int $customFieldMappingId
     * @return CustomFieldMappingException
     */
    public static function createForDefaultCustomFieldMapping($customFieldMappingId)
    {
        return new self(sprintf('Custom field mapping with id: %s is default and can not be modified.', $customFieldMappingId));
    }
}