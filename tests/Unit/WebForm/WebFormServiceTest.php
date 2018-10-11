<?php
namespace GetResponse\Tests\Unit\WebForm;

use GetResponse\Tests\Unit\BaseTestCase;
use GetResponse\WebForm\WebForm;
use GetResponse\WebForm\WebFormDto;
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

    /**
     * @test
     */
    public function shouldUpdateWebForm()
    {
        $webFormDto = new WebFormDto('webFormId1', 'top', 'myStyle', '1');

        $webFormCollection = new WebFormCollection();
        $webFormCollection->add(
            new GrWebForm(
                'webFormId1',
                'webFormName2',
                'http://getresponse.com/webform/webFormId1',
                'contactListId1',
                'enabled'
            ));
        $webFormCollection->add(
            new GrWebForm(
                'webFormId2',
                'webFormName2',
                'http://getresponse.com/webform/webFormId2',
                'contactListId2',
                'disabled'
            ));

        $this->grWebFormService
            ->expects(self::once())
            ->method('getAllWebForms')
            ->willReturn($webFormCollection);

        $webFrom = new WebForm('webFormId1', 'yes', 'top', 'myStyle', 'http://getresponse.com/webform/webFormId1');

        $this->repository
            ->expects(self::once())
            ->method('update')
            ->with($webFrom);

        $this->sut->updateWebForm($webFormDto);
    }

    /**
     * @test
     */
    public function shouldUpdateWebFormWithDefaultValues()
    {
        $webFormDto = new WebFormDto('webFormId2', '', '', '0');

        $webFormCollection = new WebFormCollection();
        $webFormCollection->add(
            new GrWebForm(
                'webFormId1',
                'webFormName2',
                'http://getresponse.com/webform/webFormId1',
                'contactListId1',
                'enabled'
            ));
        $webFormCollection->add(
            new GrWebForm(
                'webFormId2',
                'webFormName2',
                'http://getresponse.com/webform/webFormId2',
                'contactListId2',
                'disabled'
            ));

        $webFrom = new WebForm('webFormId2', 'no', 'home', 'webform', '');

        $this->repository
            ->expects(self::once())
            ->method('update')
            ->with($webFrom);

        $this->sut->updateWebForm($webFormDto);
    }

    protected function setUp()
    {
        $this->repository = $this->getMockWithoutConstructing(WebFormRepository::class);
        $this->grWebFormService = $this->getMockWithoutConstructing(GrWebFormService::class);
        $this->sut = new WebFormService($this->repository, $this->grWebFormService);
    }
}
