<?php
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
        $webForm = new WebForm(WebForm::ACTIVE, 'webFormId1', 'top', 'myStyle');

        $webFormCollection = new WebFormCollection();
        $webFormCollection->add(
            new GrWebForm(
                'webFormId1',
                'webFormName2',
                'http://getresponse.com/webform/webFormId1',
                'contactListId1',
                'enabled',
                GrWebForm::VERSION_V1
            ));
        $webFormCollection->add(
            new GrWebForm(
                'webFormId2',
                'webFormName2',
                'http://getresponse.com/webform/webFormId2',
                'contactListId2',
                'disabled',
                GrWebForm::VERSION_V1
            ));

        $this->grWebFormService
            ->expects(self::once())
            ->method('getAllWebForms')
            ->willReturn($webFormCollection);

        $expectedWebFrom = new WebForm(WebForm::ACTIVE, 'webFormId1', 'top', 'myStyle', 'http://getresponse.com/webform/webFormId1');

        $this->repository
            ->expects(self::once())
            ->method('update')
            ->with($expectedWebFrom);

        $this->sut->updateWebForm($webForm);
    }

    /**
     * @test
     */
    public function shouldUpdateWebFormWithDefaultValues()
    {
        $status = WebForm::INACTIVE;
        $webFromId = 'X3d93';
        $sidebar = 'home';

        $webForm = new WebForm($status, $webFromId, $sidebar);
        $expectedWebFrom = new WebForm($status, $webFromId, $sidebar, WebForm::STYLE_DEFAULT, '');

        $this->repository
            ->expects(self::once())
            ->method('update')
            ->with($expectedWebFrom);

        $this->sut->updateWebForm($webForm);
    }
}
