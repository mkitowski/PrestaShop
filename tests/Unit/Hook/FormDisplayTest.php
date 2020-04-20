<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author     Getresponse <grintegrations@getresponse.com>
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

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
            WebForm::STATUS_ACTIVE,
            'webFormId1',
            'bottom',
            'default',
            'http://getresponse.com/webform/webFormId1'
        );

        $webFormWithDisabledStatus = new WebForm(
            WebForm::STATUS_INACTIVE,
            'webFormId1',
            'top',
            'default',
            'http://getresponse.com/webform/webFormId1'
        );

        $webFormWithDefaultStyle = new WebForm(
            WebForm::STATUS_ACTIVE,
            'webFormId1',
            'top',
            'default',
            'http://getresponse.com/webform/webFormId1'
        );

        $webFormWithPrestaShopStyle = new WebForm(
            WebForm::STATUS_ACTIVE,
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
