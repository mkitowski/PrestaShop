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
            'value' => 'value',
            'name' => 'name',
            'active' => 1,
            'field' => 'field',
            'default' => 'default',
        ];

        $expected = new CustomFieldMapping(
            $request['id'],
            $request['value'],
            $request['name'],
            'yes',
            '',
            $request['default']
        );

        $this->assertEquals($expected, CustomFieldMapping::createFromRequest($request));
    }

    /**
     * @test
     */
    public function shouldCreateCustomFieldMappingFromRequestWithInactiveStatus()
    {
        $request = [
            'id' => 'id',
            'value' => 'value',
            'name' => 'name',
            'active' => 0,
            'field' => 'field',
            'default' => 'default',
        ];

        $expected = new CustomFieldMapping(
            $request['id'],
            $request['value'],
            $request['name'],
            'no',
            '',
            $request['default']
        );

        $this->assertEquals($expected, CustomFieldMapping::createFromRequest($request));
    }
}
