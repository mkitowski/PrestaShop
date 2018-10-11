<?php
namespace GetResponse\Tests\Unit\Export;

use GetResponse\Export\ExportSettings;
use GetResponse\Export\ExportValidator;
use GetResponse\Tests\Unit\BaseTestCase;

class ExportValidatorTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldReturnNoError()
    {
        $exportSettings = new ExportSettings(
            'contactListId',
            '3',
            true,
            true,
            true,
            'shopId'
        );

        $validator = new ExportValidator($exportSettings);
        $this->assertTrue($validator->isValid());
        $this->assertEmpty($validator->getErrors());
    }

    /**
     * @test
     */
    public function shouldReturnError()
    {
        $exportSettings = new ExportSettings(
            '',
            '3',
            true,
            true,
            true,
            'shopId'
        );

        $validator = new ExportValidator($exportSettings);
        $this->assertFalse($validator->isValid());
        $this->assertEquals(['You need to select list'], $validator->getErrors());

        $exportSettings = new ExportSettings(
            'contactListId',
            '3',
            true,
            true,
            true,
            ''
        );

        $validator = new ExportValidator($exportSettings);
        $this->assertFalse($validator->isValid());
        $this->assertEquals(['You need to select store'], $validator->getErrors());
    }

}