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
            'gr_custom_id' => 'test',
            'is_default' => 0,
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
            'gr_custom_id' => 'test',
            'is_default' => 1,
        ];

        $validator = new CustomFieldMappingValidator($requestData);
        $this->assertFalse($validator->isValid());
        $this->assertEquals(['Default mappings cannot be changed!'], $validator->getErrors());

        $requestData = [
            'gr_custom_id' => 'tes$#t',
            'is_default' => 0,
        ];

        $validator = new CustomFieldMappingValidator($requestData);
        $this->assertFalse($validator->isValid());
        $this->assertEquals(['Custom field contains invalid characters!'], $validator->getErrors());
    }
}
