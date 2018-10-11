<?php
namespace GetResponse\Tests\Unit\WebForm;

use GetResponse\Tests\Unit\BaseTestCase;
use GetResponse\WebForm\WebFormDto;
use GetResponse\WebForm\WebFormValidator;

/**
 * Class WebFormValidatorTest
 * @package GetResponse\Tests\Unit\WebForm
 */
class WebFormValidatorTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldReturnNoError()
    {
        $webFormDto = new WebFormDto('formId', 'bottom', 'myStyle', '1');

        $validator = new WebFormValidator($webFormDto);
        $this->assertTrue($validator->isValid());
        $this->assertEmpty($validator->getErrors());
    }

    /**
     * @test
     */
    public function shouldReturnError()
    {
        $webFormDto = new WebFormDto('', 'bottom', 'myStyle', '1');

        $validator = new WebFormValidator($webFormDto);
        $this->assertFalse($validator->isValid());
        $this->assertEquals(['You need to select a form and its placement'], $validator->getErrors());


        $webFormDto = new WebFormDto('formId', '', 'myStyle', '1');

        $validator = new WebFormValidator($webFormDto);
        $this->assertFalse($validator->isValid());
        $this->assertEquals(['You need to select a form and its placement'], $validator->getErrors());
    }

}
