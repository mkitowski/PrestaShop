<?php
namespace GetResponse\CustomFields;

/**
 * Class CustomFieldsServiceFactory
 */
class CustomFieldsServiceFactory
{
    /**
     * @return CustomFieldService
     */
    public static function create()
    {
        return new CustomFieldService(new CustomFieldsRepository());
    }
}
