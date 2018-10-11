<?php
namespace GetResponse\Tests\Unit\Hook;

use GetResponse\Hook\FormDisplay;
use GetResponse\Tests\Unit\BaseTestCase;
use GetResponse\WebForm\WebForm;
use GetResponse\WebForm\WebFormService;
use PrestaShopDatabaseException;

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
    public function shouldNotDisplayWebFormIfDatabaseException()
    {
        $this->webFormService
            ->expects(self::once())
            ->method('getWebForm')
            ->willThrowException(new PrestaShopDatabaseException());

        $this->assertEmpty($this->sut->displayWebForm('top'));
    }

    /**
     * @test
     */
    public function shouldNotDisplayWebFormIfNotEligible()
    {
        $webFormWithDifferentPosition = new WebForm(
            'webFormId1',
            'yes',
            'bottom',
            'default',
            'http://getresponse.com/webform/webFormId1'
        );

        $webFormWithDisabledStatus = new WebForm(
            'webFormId1',
            'no',
            'top',
            'default',
            'http://getresponse.com/webform/webFormId1'
        );

        $webFormWithDefaultStyle = new WebForm(
            'webFormId1',
            'yes',
            'top',
            'default',
            'http://getresponse.com/webform/webFormId1'
        );

        $webFormWithPrestaShopStyle = new WebForm(
            'webFormId1',
            'yes',
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
