<?php
namespace GetResponse\Tests\Unit\CustomFields;

use GetResponse\CustomFields\CustomFieldService;
use GetResponse\CustomFieldsMapping\CustomFieldMapping;
use GetResponse\CustomFieldsMapping\CustomFieldMappingCollection;
use GetResponse\Tests\Unit\BaseTestCase;
use GrShareCode\CustomField\CustomFieldService as GrCustomFieldService;

class CustomFieldServiceTest extends BaseTestCase
{
    /** @var GrCustomFieldService | \PHPUnit_Framework_MockObject_MockObject */
    private $grCustomFieldService;

    /** @var CustomFieldService */
    private $sut;

    /**
     * @test
     */
    public function shouldNotCreateCustomFieldsIfCollectionEmpty()
    {
        $customFieldMappingCollection = new CustomFieldMappingCollection();

        $this->grCustomFieldService
            ->expects(self::never())
            ->method('getCustomFieldByName');

        $this->grCustomFieldService
            ->expects(self::never())
            ->method('createCustomField');

        $this->sut->addCustomsIfMissing($customFieldMappingCollection);
    }


    /**
     * @test
     */
    public function shouldNotCreateCustomFieldsIfCustomFieldAlreadyExist()
    {
        $customFieldMappingCollection = new CustomFieldMappingCollection();
        $customFieldMappingCollection->add(
            new CustomFieldMapping('id', 'value', 'name', 'yes', 'name', 'no')
        );
        $customFieldMappingCollection->add(
            new CustomFieldMapping('id2', 'value2', 'name2', 'yes2', 'name2', 'no2')
        );

        $this->grCustomFieldService
            ->expects(self::exactly(2))
            ->method('getCustomFieldByName')
            ->willReturn(['customFieldsId' => 'customFieldValue']);

        $this->grCustomFieldService
            ->expects(self::never())
            ->method('createCustomField');

        $this->sut->addCustomsIfMissing($customFieldMappingCollection);
    }

    /**
     * @test
     */
    public function shouldCreateCustomFields()
    {
        $customFieldMappingCollection = new CustomFieldMappingCollection();
        $customFieldMappingCollection->add(
            new CustomFieldMapping('id', 'value', 'name', 'yes', 'name', 'no')
        );
        $customFieldMappingCollection->add(
            new CustomFieldMapping('id2', 'value2', 'name2', 'yes2', 'name2', 'no2')
        );

        $this->grCustomFieldService
            ->expects(self::exactly(2))
            ->method('getCustomFieldByName')
            ->willReturn(null);

        $this->grCustomFieldService
            ->expects(self::exactly(2))
            ->method('createCustomField');

        $this->sut->addCustomsIfMissing($customFieldMappingCollection);
    }

    protected function setUp()
    {
        $this->grCustomFieldService = $this->getMockWithoutConstructing(GrCustomFieldService::class);
        $this->sut = new CustomFieldService($this->grCustomFieldService);
    }


}
