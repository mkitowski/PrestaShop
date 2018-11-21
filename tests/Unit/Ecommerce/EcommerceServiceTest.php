<?php
namespace GetResponse\Tests\Unit\Ecommerce;

use GetResponse\Account\AccountSettings;
use GetResponse\Ecommerce\Ecommerce;
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
        $ecommerce = new Ecommerce('active', 'shopId', 'grListId');

        $this->repository
            ->expects(self::once())
            ->method('updateEcommerceSubscription')
            ->with($ecommerce);

        $this->sut->updateEcommerceDetails($ecommerce);
    }

    /**
     * @test
     */
    public function shouldUpdateEcommerceDetailsWithoutShopId()
    {
        $ecommerce = new Ecommerce('inactive', 'shopId', 'grListId');

        $this->repository
            ->expects(self::once())
            ->method('updateEcommerceSubscription')
            ->with($ecommerce);

        $this->sut->updateEcommerceDetails($ecommerce);
    }

    /**
     * @test
     */
    public function shouldReturnSettings()
    {
        $this->repository
            ->expects(self::exactly(2))
            ->method('getEcommerceSettings')
            ->willReturnOnConsecutiveCalls(new Ecommerce(Ecommerce::STATUS_INACTIVE, null, null), new Ecommerce(Ecommerce::STATUS_ACTIVE, 'getResponseShopId', 'grListId'));

        $this->assertFalse($this->sut->getEcommerceSettings()->isEnabled());
        $this->assertTrue($this->sut->getEcommerceSettings()->isEnabled());
    }

}
