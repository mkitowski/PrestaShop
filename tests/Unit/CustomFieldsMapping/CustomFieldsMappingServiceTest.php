<?php
namespace GetResponse\Tests\Unit\CustomFieldsMapping;

use GetResponse\CustomFieldsMapping\CustomFieldMapping;
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
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );

        /*
        $customFieldMapping = new CustomFieldMapping(
            'id',
            'value',
            'company',
            'yes',
            'field',
            'no'
        );

        $this->repository->
        $this->sut->updateCustomFieldMapping($customFieldMapping);
        */
    }
}
