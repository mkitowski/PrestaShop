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

namespace GetResponse\Tests\Unit\WebForm;

use GetResponse\Tests\Unit\BaseTestCase;
use GetResponse\WebForm\WebForm;
use GetResponse\WebForm\WebFormRepository;
use GetResponse\WebForm\WebFormService;
use GrShareCode\WebForm\WebForm as GrWebForm;
use GrShareCode\WebForm\WebFormCollection;
use GrShareCode\WebForm\WebFormService as GrWebFormService;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class WebFormServiceTest
 * @package GetResponse\Tests\Unit\WebForm
 */
class WebFormServiceTest extends BaseTestCase
{
    /** @var WebFormRepository | PHPUnit_Framework_MockObject_MockObject */
    private $repository;

    /** @var GrWebFormService | PHPUnit_Framework_MockObject_MockObject */
    private $grWebFormService;

    /** @var WebFormService */
    private $sut;

    protected function setUp()
    {
        $this->repository = $this->getMockWithoutConstructing(WebFormRepository::class);
        $this->grWebFormService = $this->getMockWithoutConstructing(GrWebFormService::class);
        $this->sut = new WebFormService($this->repository, $this->grWebFormService);
    }

    /**
     * @test
     */
    public function shouldUpdateWebForm()
    {
        $webForm = new WebForm(WebForm::STATUS_ACTIVE, 'webFormId1', 'top', 'myStyle');

        $webFormCollection = new WebFormCollection();
        $webFormCollection->add(
            new GrWebForm(
                'webFormId1',
                'webFormName2',
                'http://getresponse.com/webform/webFormId1',
                'contactListId1',
                'enabled',
                GrWebForm::VERSION_V1
            )
        );
        $webFormCollection->add(
            new GrWebForm(
                'webFormId2',
                'webFormName2',
                'http://getresponse.com/webform/webFormId2',
                'contactListId2',
                'disabled',
                GrWebForm::VERSION_V1
            )
        );

        $this->grWebFormService
            ->expects(self::once())
            ->method('getAllWebForms')
            ->willReturn($webFormCollection);

        $expectedWebFrom = new WebForm(
            WebForm::STATUS_ACTIVE,
            'webFormId1',
            'top',
            'myStyle',
            'http://getresponse.com/webform/webFormId1'
        );

        $this->repository
            ->expects(self::once())
            ->method('update')
            ->with($expectedWebFrom);

        $this->sut->updateWebForm($webForm);
    }

    /**
     * @test
     */
    public function shouldClearWebForm()
    {
        $status = WebForm::STATUS_INACTIVE;
        $webFromId = 'X3d93';
        $sidebar = 'home';

        $webForm = new WebForm($status, $webFromId, $sidebar);

        $this->repository
            ->expects(self::once())
            ->method('clearSettings');

        $this->sut->updateWebForm($webForm);
    }
}
