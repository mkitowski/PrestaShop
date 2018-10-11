<?php
namespace GetResponse\Tests\Unit\CustomFieldsMapping;

use GetResponse\CustomFieldsMapping\CustomFieldMappingValidator;
use GetResponse\Tests\Unit\BaseTestCase;

class CustomFieldMappingValidatorTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldReturnNoError()
    {
        $requestData = [
            'name' => 'test',
            'default' => 0,
        ];

        $validator = new CustomFieldMappingValidator($requestData);
        $this->assertTrue($validator->isValid());
        $this->assertEmpty($validator->getErrors());
    }

    /**
     * @test
     */
    public function shouldReturnError()
    {
        $requestData = [
            'name' => 'test',
            'default' => 1,
        ];

        $validator = new CustomFieldMappingValidator($requestData);
        $this->assertFalse($validator->isValid());
        $this->assertEquals(['Default mappings cannot be changed!'], $validator->getErrors());

        $requestData = [
            'name' => 'tes$#t',
            'default' => 0,
        ];

        $validator = new CustomFieldMappingValidator($requestData);
        $this->assertFalse($validator->isValid());
        $this->assertEquals(['Custom field contains invalid characters!'], $validator->getErrors());
    }
}