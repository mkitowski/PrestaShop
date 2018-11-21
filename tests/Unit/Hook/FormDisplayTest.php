<?php
namespace GetResponse\Tests\Unit\Hook;

use GetResponse\Hook\FormDisplay;
use GetResponse\Tests\Unit\BaseTestCase;
use GetResponse\WebForm\WebForm;
use GetResponse\WebForm\WebFormService;

class FormDisplayTest extends BaseTestCase
{

    /** @var WebFormService | \PHPUnit_Framework_MockObject_MockObject */
    private $webFormService;

    /** @var FormDisplay */
    private $sut;

    /**
     * @test
     */
    public function shouldNotDisplayWebFormIfEmptyPosition()
    {
        $this->assertEmpty($this->sut->displayWebForm(''));
    }

    /**
     * @test
     */
    public function shouldNotDisplayWebFormIfNotEligible()
    {
        $webFormWithDifferentPosition = new WebForm(
            WebForm::ACTIVE,
            'webFormId1',
            'bottom',
            'default',
            'http://getresponse.com/webform/webFormId1'
        );

        $webFormWithDisabledStatus = new WebForm(
            WebForm::INACTIVE,
            'webFormId1',
            'top',
            'default',
            'http://getresponse.com/webform/webFormId1'
        );

        $webFormWithDefaultStyle = new WebForm(
            WebForm::ACTIVE,
            'webFormId1',
            'top',
            'default',
            'http://getresponse.com/webform/webFormId1'
        );

        $webFormWithPrestaShopStyle = new WebForm(
            WebForm::ACTIVE,
            'webFormId1',
            'top',
            'prestashop',
            'http://getresponse.com/webform/webFormId1'
        );

        $resultArrayWithDefaultStyle = [
            'webform_url' => 'http://getresponse.com/webform/webFormId1',
            'style' => null,
            'position' => 'top'
        ];

        $resultArrayWithPrestaShopStyle = [
            'webform_url' => 'http://getresponse.com/webform/webFormId1',
            'style' => '&css=1',
            'position' => 'top'
        ];

        $this->webFormService
            ->expects(self::exactly(5))
            ->method('getWebForm')
            ->willReturnOnConsecutiveCalls(
                null,
                $webFormWithDifferentPosition,
                $webFormWithDisabledStatus,
                $webFormWithDefaultStyle,
                $webFormWithPrestaShopStyle
            );

        $this->assertEmpty($this->sut->displayWebForm('top'));
        $this->assertEmpty($this->sut->displayWebForm('top'));
        $this->assertEmpty($this->sut->displayWebForm('top'));
        $this->assertEquals($resultArrayWithDefaultStyle, $this->sut->displayWebForm('top'));
        $this->assertEquals($resultArrayWithPrestaShopStyle, $this->sut->displayWebForm('top'));

    }

    protected function setUp()
    {
        $this->webFormService = $this->getMockWithoutConstructing(WebFormService::class);
        $this->sut = new FormDisplay($this->webFormService);
    }
}
