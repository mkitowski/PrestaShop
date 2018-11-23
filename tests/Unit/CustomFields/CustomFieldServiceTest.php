<?php
namespace GetResponse\Tests\Unit\CustomFields;

use GetResponse\CustomFields\CustomFieldService;
use GetResponse\Tests\Unit\BaseTestCase;
use GrShareCode\CustomField\CustomField;
use GrShareCode\CustomField\CustomFieldCollection;
use GrShareCode\CustomField\CustomFieldService as GrCustomFieldService;

class CustomFieldServiceTest extends BaseTestCase
{
    /** @var GrCustomFieldService | \PHPUnit_Framework_MockObject_MockObject */
    private $grCustomFieldService;

    /** @var CustomFieldService */
    private $sut;

    protected function setUp()
    {
        $this->grCustomFieldService = $this->getMockWithoutConstructing(GrCustomFieldService::class);
        $this->sut = new CustomFieldService($this->grCustomFieldService);
    }

    /**
     * @test
     */
    public function shouldReturnCustomFields()
    {
        $collection = new CustomFieldCollection();
        $collection->add(new CustomField('d4s2', 'testCustom', 'text', 'null'));

        $this->grCustomFieldService
            ->expects(self::once())
            ->method('getAllCustomFields')
            ->willReturn($collection);

        self::assertEquals($collection, $this->sut->getCustomFieldsFromGetResponse());
    }

}
