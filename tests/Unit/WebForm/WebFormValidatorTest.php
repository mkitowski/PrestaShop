<?php
namespace GetResponse\Tests\Unit\WebForm;

use GetResponse\Tests\Unit\BaseTestCase;
use GetResponse\WebForm\WebForm;
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
        $webForm = new WebForm('formId', 'bottom', 'myStyle', '1');

        $validator = new WebFormValidator($webForm);
        $this->assertTrue($validator->isValid());
        $this->assertEmpty($validator->getErrors());
    }

    /**
     * @test
     */
    public function shouldReturnError()
    {
        $webForm = new WebForm(WebForm::ACTIVE, '', 'bottom', 'myStyle');

        $validator = new WebFormValidator($webForm);
        $this->assertFalse($validator->isValid());
        $this->assertEquals(['You need to select a form and its placement'], $validator->getErrors());


        $webForm = new WebForm(WebForm::ACTIVE,'formId', '', 'myStyle');

        $validator = new WebFormValidator($webForm);
        $this->assertFalse($validator->isValid());
        $this->assertEquals(['You need to select a form and its placement'], $validator->getErrors());
    }

}
