<?php
namespace GetResponse\Tests\Unit\CustomFields;

use GetResponse\CustomFields\CustomFieldService;
use GetResponse\CustomFields\CustomFieldsRepository;
use GetResponse\Tests\Unit\BaseTestCase;
use GrShareCode\CustomField\CustomField;
use GrShareCode\CustomField\CustomFieldCollection;
use GrShareCode\CustomField\CustomFieldService as GrCustomFieldService;

class CustomFieldServiceTest extends BaseTestCase
{
    /** @var CustomFieldsRepository | \PHPUnit_Framework_MockObject_MockObject */
    private $customFieldsRepository;

    /** @var CustomFieldService */
    private $sut;

    protected function setUp()
    {
        $this->customFieldsRepository = $this->getMockWithoutConstructing(CustomFieldsRepository::class);
        $this->sut = new CustomFieldService($this->customFieldsRepository);
    }

    /**
     * @test
     */
    public function shouldReturnCustomFields()
    {
        $collection = new CustomFieldCollection();
        $collection->add(new CustomField('d4s2', 'testCustom', 'text', 'null'));

        $this->customFieldsRepository
            ->expects(self::once())
            ->method('getCustomFieldsMapping')
            ->willReturn($collection);

        self::assertEquals($collection, $this->sut->getCustomFieldsMapping());
    }

}
