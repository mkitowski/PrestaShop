<?php
namespace GetResponse\Tests\Unit\Ecommerce;

use GetResponse\Account\AccountSettings;
use GetResponse\Ecommerce\Ecommerce;
use GetResponse\Ecommerce\EcommerceDto;
use GetResponse\Ecommerce\EcommerceRepository;
use GetResponse\Ecommerce\EcommerceService;
use GetResponse\Tests\Unit\BaseTestCase;
use GrShareCode\Shop\ShopService;
use PHPUnit_Framework_MockObject_MockObject;

class EcommerceServiceTest extends BaseTestCase
{
    /** @var EcommerceService */
    private $sut;

    /** @var EcommerceRepository | PHPUnit_Framework_MockObject_MockObject */
    private $repository;

    /** @var ShopService | PHPUnit_Framework_MockObject_MockObject */
    private $shopService;

    /** @var AccountSettings| PHPUnit_Framework_MockObject_MockObject */
    private $accountSettings;

    protected function setUp()
    {
        $this->repository = $this->getMockWithoutConstructing(EcommerceRepository::class);
        $this->shopService = $this->getMockWithoutConstructing(ShopService::class);
        $this->accountSettings = $this->getMockWithoutConstructing(AccountSettings::class);

        $this->sut = new EcommerceService($this->repository, $this->shopService, $this->accountSettings);
    }

    /**
     * @test
     */
    public function shouldUpdateEcommerceDetails()
    {
        $shopId = 'shopId';

        $ecommerceDto = new EcommerceDto($shopId, '1');

        $this->repository
            ->expects(self::once())
            ->method('updateEcommerceSubscription')
            ->with(true);

        $this->repository
            ->expects(self::once())
            ->method('updateEcommerceShopId')
            ->with($shopId);

        $this->sut->updateEcommerceDetails($ecommerceDto);
    }

    /**
     * @test
     */
    public function shouldUpdateEcommerceDetailsWithoutShopId()
    {
        $shopId = 'shopId';

        $ecommerceDto = new EcommerceDto($shopId, '0');

        $this->repository
            ->expects(self::once())
            ->method('updateEcommerceSubscription')
            ->with(false);

        $this->repository
            ->expects(self::never())
            ->method('updateEcommerceShopId');

        $this->sut->updateEcommerceDetails($ecommerceDto);
    }

    /**
     * @test
     */
    public function shouldReturnSettings()
    {
        $this->repository
            ->expects(self::exactly(2))
            ->method('getEcommerceSettings')
            ->willReturnOnConsecutiveCalls(null, new Ecommerce('prestaShopId', 'getResponseShopId'));

        $this->assertFalse($this->sut->isEcommerceEnabled());
        $this->assertTrue($this->sut->isEcommerceEnabled());
    }

}
