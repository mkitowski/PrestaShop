<?php
namespace GetResponse\Tests\Unit\CustomFieldsMapping;

use GetResponse\CustomFieldsMapping\CustomFieldMapping;
use GetResponse\CustomFieldsMapping\CustomFieldMappingCollection;
use GetResponse\CustomFieldsMapping\CustomFieldMappingException;
use GetResponse\CustomFieldsMapping\CustomFieldsMappingService;
use GetResponse\Tests\Unit\BaseTestCase;
use GetResponseRepository;
use PHPUnit_Framework_MockObject_MockObject;

class CustomFieldsMappingServiceTest extends BaseTestCase
{
    /** @var CustomFieldsMappingService */
    private $sut;

    /** @var GetResponseRepository | PHPUnit_Framework_MockObject_MockObject */
    private $repository;

    protected function setUp()
    {
        $this->repository = $this->getMockWithoutConstructing(GetResponseRepository::class);
        $this->sut = new CustomFieldsMappingService($this->repository);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenCustomFieldMappingNotFound()
    {
        $this->expectException(CustomFieldMappingException::class);
        $this->expectExceptionMessage('Custom field mapping not found with id: 10.');

        $customFieldMapping = new CustomFieldMapping(
            10,
            'value',
            'company',
            'yes',
            'field',
            'no'
        );

        $this->repository
            ->expects(self::once())
            ->method('getCustomFieldsMapping')
            ->willReturn([]);

        $this->sut->updateCustomFieldMapping($customFieldMapping);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionForDefaultCustomFieldMapping()
    {
        $this->expectException(CustomFieldMappingException::class);
        $this->expectExceptionMessage('Custom field mapping with id: 10');

        $customFieldMapping = new CustomFieldMapping(
            10,
            'value',
            'company',
            'yes',
            'field',
            'no'
        );

        $collection = new CustomFieldMappingCollection();
        $collection->add($customFieldMapping);
        $this->repository
            ->expects(self::once())
            ->method('getCustomFieldsMapping')
            ->willReturn($collection);

        $this->sut->updateCustomFieldMapping($customFieldMapping);
    }

    /**
     * @test
     */
    public function shouldUpdateCustom()
    {
        $customFieldMapping = new CustomFieldMapping(
            10,
            'value',
            'company',
            'yes',
            true,
            false
        );

        $collection = new CustomFieldMappingCollection();
        $collection->add($customFieldMapping);

        $this->repository
            ->expects(self::once())
            ->method('getCustomFieldsMapping')
            ->willReturn($collection);

        $this->repository
            ->expects(self::once())
            ->method('updateCustom')
            ->with($customFieldMapping);

        $this->sut->updateCustomFieldMapping($customFieldMapping);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyCustomFieldMappingCollection()
    {
        $this->repository
            ->expects(self::once())
            ->method('getCustomFieldsMapping')
            ->willReturn(new CustomFieldMappingCollection());

        $this->assertEquals(new CustomFieldMappingCollection(), $this->sut->getActiveCustomFieldMapping());
    }

    /**
     * @test
     */
    public function shouldReturnCustomFieldMappingCollection()
    {
        $proper_custom_field_mapping1 = [
            'id' => 1,
            'custom_name' => 'address',
            'customer_property_name' => 'address',
            'gr_custom_id' => 'X3d9k',
            'is_active' => true,
            'is_default' => false
        ];

        $inactive_custom_field_mapping = [
            'id' => 2,
            'custom_name' => 'address',
            'customer_property_name' => 'address',
            'gr_custom_id' => 'X3d9k',
            'is_active' => true,
            'is_default' => true
        ];

        $proper_custom_field_mapping_2 = [
            'id' => 3,
            'custom_name' => 'company',
            'customer_property_name' => 'company',
            'gr_custom_id' => 'X3d9k',
            'is_active' => true,
            'is_default' => false
        ];

        $customFieldMappingCollection = new CustomFieldMappingCollection();
        $customFieldMappingCollection->add(
            new CustomFieldMapping(
                $proper_custom_field_mapping1['id'],
                $proper_custom_field_mapping1['custom_name'],
                $proper_custom_field_mapping1['customer_property_name'],
                $proper_custom_field_mapping1['gr_custom_id'],
                $proper_custom_field_mapping1['is_active'],
                $proper_custom_field_mapping1['is_default']
            )
        );

        $customFieldMappingCollection->add(
            new CustomFieldMapping(
                $inactive_custom_field_mapping['id'],
                $inactive_custom_field_mapping['custom_name'],
                $inactive_custom_field_mapping['customer_property_name'],
                $inactive_custom_field_mapping['gr_custom_id'],
                $inactive_custom_field_mapping['is_active'],
                $inactive_custom_field_mapping['is_default']
            )
        );

        $customFieldMappingCollection->add(
            new CustomFieldMapping(
                $proper_custom_field_mapping_2['id'],
                $proper_custom_field_mapping_2['custom_name'],
                $proper_custom_field_mapping_2['customer_property_name'],
                $proper_custom_field_mapping_2['gr_custom_id'],
                $proper_custom_field_mapping_2['is_active'],
                $proper_custom_field_mapping_2['is_default']
            )
        );

        $expectedCustomFieldMappingCollection = new CustomFieldMappingCollection();
        $expectedCustomFieldMappingCollection->add(
            new CustomFieldMapping(
                $proper_custom_field_mapping1['id'],
                $proper_custom_field_mapping1['custom_name'],
                $proper_custom_field_mapping1['customer_property_name'],
                $proper_custom_field_mapping1['gr_custom_id'],
                $proper_custom_field_mapping1['is_active'],
                $proper_custom_field_mapping1['is_default']
            )
        );
        $expectedCustomFieldMappingCollection->add(
            new CustomFieldMapping(
                $proper_custom_field_mapping_2['id'],
                $proper_custom_field_mapping_2['custom_name'],
                $proper_custom_field_mapping_2['customer_property_name'],
                $proper_custom_field_mapping_2['gr_custom_id'],
                $proper_custom_field_mapping_2['is_active'],
                $proper_custom_field_mapping_2['is_default']
            )
        );

        $this->repository
            ->expects(self::once())
            ->method('getCustomFieldsMapping')
            ->willReturn($customFieldMappingCollection);

        $this->assertEquals($expectedCustomFieldMappingCollection, $this->sut->getActiveCustomFieldMapping());
    }
}
