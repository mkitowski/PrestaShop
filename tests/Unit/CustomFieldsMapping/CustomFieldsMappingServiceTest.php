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

    /**
     * @test
     */
    public function shouldThrowExceptionWhenCustomFieldMappingNotFound()
    {
        $this->expectException(CustomFieldMappingException::class);
        $this->expectExceptionMessage('Custom field mapping not found with id: id.');

        $customFieldMapping = new CustomFieldMapping(
            'id',
            'value',
            'company',
            'yes',
            'field',
            'no'
        );

        $this->repository
            ->expects(self::once())
            ->method('getCustoms')
            ->willReturn([]);

        $this->sut->updateCustomFieldMapping($customFieldMapping);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionForDefaultCustomFieldMapping()
    {
        $this->expectException(CustomFieldMappingException::class);
        $this->expectExceptionMessage('Custom field mapping with id: emailId is default and can not be modified.');

        $customFieldMapping = new CustomFieldMapping(
            'emailId',
            'value',
            'company',
            'yes',
            'field',
            'no'
        );

        $this->repository
            ->expects(self::once())
            ->method('getCustoms')
            ->willReturn(
                [
                    [
                        'id_custom' => 'emailId',
                        'custom_value' => 'email',
                        'custom_name' => 'email',
                        'active_custom' => 'yes',
                        'custom_field' => 'email',
                        'default' => 'yes'
                    ]
                ]
            );
        $this->sut->updateCustomFieldMapping($customFieldMapping);
    }

    /**
     * @test
     */
    public function shouldUpdateCustom()
    {
        $customFieldMapping = new CustomFieldMapping(
            'addressId',
            'value',
            'company',
            'yes',
            'field',
            'no'
        );

        $this->repository
            ->expects(self::once())
            ->method('getCustoms')
            ->willReturn(
                [
                    [
                        'id_custom' => 'addressId',
                        'custom_value' => 'address',
                        'custom_name' => 'address',
                        'active_custom' => 'no',
                        'custom_field' => '',
                        'default' => 'false'
                    ]
                ]
            );

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
            ->method('getCustoms')
            ->willReturn(
                [
                    'default_custom_field_mapping' => [
                        'id_custom' => 'addressId',
                        'custom_value' => 'address',
                        'custom_name' => 'address',
                        'active_custom' => 'yes',
                        'custom_field' => '',
                        'default' => 'yes'
                    ],
                    'inactive_custom_field_mapping' => [
                        'id_custom' => 'addressId',
                        'custom_value' => 'address',
                        'custom_name' => 'address',
                        'active_custom' => 'no',
                        'custom_field' => '',
                        'default' => 'false'
                    ]
                ]
            );

        $this->assertEquals(new CustomFieldMappingCollection(), $this->sut->getActiveCustomFieldMapping());
    }

    /**
     * @test
     */
    public function shouldReturnCustomFieldMappingCollection()
    {
        $proper_custom_field_mapping1 = [
            'id_custom' => 'addressId',
            'custom_value' => 'address',
            'custom_name' => 'address',
            'active_custom' => 'yes',
            'custom_field' => '',
            'default' => 'no'
        ];

        $inactive_custom_field_mapping = [
            'id_custom' => 'addressId',
            'custom_value' => 'address',
            'custom_name' => 'address',
            'active_custom' => 'no',
            'custom_field' => '',
            'default' => 'false'
        ];

        $proper_custom_field_mapping_2 = [
            'id_custom' => 'companyId',
            'custom_value' => 'company',
            'custom_name' => 'company',
            'active_custom' => 'yes',
            'custom_field' => '',
            'default' => 'false'
        ];

        $this->repository
            ->expects(self::once())
            ->method('getCustoms')
            ->willReturn([
                $proper_custom_field_mapping1,
                $inactive_custom_field_mapping,
                $proper_custom_field_mapping_2,
            ]);

        $customFieldMappingCollection = new CustomFieldMappingCollection();
        $customFieldMappingCollection->add(
            new CustomFieldMapping(
                $proper_custom_field_mapping1['id_custom'],
                $proper_custom_field_mapping1['custom_value'],
                $proper_custom_field_mapping1['custom_name'],
                $proper_custom_field_mapping1['active_custom'],
                $proper_custom_field_mapping1['custom_field'],
                $proper_custom_field_mapping1['default']
            )
        );
        $customFieldMappingCollection->add(
            new CustomFieldMapping(
                $proper_custom_field_mapping_2['id_custom'],
                $proper_custom_field_mapping_2['custom_value'],
                $proper_custom_field_mapping_2['custom_name'],
                $proper_custom_field_mapping_2['active_custom'],
                $proper_custom_field_mapping_2['custom_field'],
                $proper_custom_field_mapping_2['default']
            )
        );

        $this->assertEquals($customFieldMappingCollection, $this->sut->getActiveCustomFieldMapping());
    }

    protected function setUp()
    {
        $this->repository = $this->getMockWithoutConstructing(GetResponseRepository::class);
        $this->sut = new CustomFieldsMappingService($this->repository);
    }

}
