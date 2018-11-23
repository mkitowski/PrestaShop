<?php
namespace GetResponse\Tests\Unit\CustomFieldsMapping;

use GetResponse\CustomFieldsMapping\CustomFieldMapping;
use GetResponse\Tests\Unit\BaseTestCase;

class CustomFieldMappingTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateCustomFieldMappingFromRequestWithActiveStatus()
    {
        $request = [
            'id' => 'id',
            'custom_name' => 'customName',
            'customer_property_name' => 'customerPropName',
            'gr_custom_id' => 'c34d',
            'is_active' => 1,
            'is_default' => 0,
        ];

        $expected = new CustomFieldMapping(
            $request['id'],
            $request['custom_name'],
            $request['customer_property_name'],
            $request['gr_custom_id'],
            true,
            false
        );

        $this->assertEquals($expected, CustomFieldMapping::createFromArray($request));
    }

    /**
     * @test
     */
    public function shouldCreateCustomFieldMappingFromRequestWithInactiveStatus()
    {
        $request = [
            'id' => 'id',
            'custom_name' => 'customName',
            'customer_property_name' => 'customerPropName',
            'gr_custom_id' => 'c34d',
            'is_active' => 0,
            'is_default' => 0,
        ];

        $expected = new CustomFieldMapping(
            $request['id'],
            $request['custom_name'],
            $request['customer_property_name'],
            $request['gr_custom_id'],
            false,
            false
        );

        $this->assertEquals($expected, CustomFieldMapping::createFromArray($request));
    }
}
