<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author     Getresponse <grintegrations@getresponse.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace GetResponse\Tests\Unit\Ecommerce;

use GetResponse\Ecommerce\Ecommerce;
use GetResponse\Ecommerce\EcommerceRepository;
use GetResponse\Ecommerce\EcommerceService;
use GetResponse\Tests\Unit\BaseTestCase;
use GrShareCode\Shop\ShopService;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class EcommerceServiceTest
 * @package GetResponse\Tests\Unit\Ecommerce
 */
class EcommerceServiceTest extends BaseTestCase
{
    /** @var EcommerceService */
    private $sut;

    /** @var EcommerceRepository | PHPUnit_Framework_MockObject_MockObject */
    private $repository;

    /** @var ShopService | PHPUnit_Framework_MockObject_MockObject */
    private $shopService;

    protected function setUp()
    {
        $this->repository = $this->getMockWithoutConstructing(EcommerceRepository::class);
        $this->shopService = $this->getMockWithoutConstructing(ShopService::class);

        $this->sut = new EcommerceService($this->repository, $this->shopService);
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
        $ecommerce = new Ecommerce(Ecommerce::STATUS_ACTIVE, 'shopId', 'grListId');

        $this->repository
            ->expects(self::once())
            ->method('updateEcommerceSubscription')
            ->with($ecommerce);

        $this->sut->updateEcommerceDetails($ecommerce);
    }

    /**
     * @test
     */
    public function shouldClearEcommerceDetails()
    {
        $ecommerce = new Ecommerce(Ecommerce::STATUS_INACTIVE, 'shopId', 'grListId');

        $this->repository
            ->expects(self::once())
            ->method('clearEcommerceSettings');

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
            ->willReturnOnConsecutiveCalls(
                new Ecommerce(Ecommerce::STATUS_INACTIVE, null, null),
                new Ecommerce(Ecommerce::STATUS_ACTIVE, 'getResponseShopId', 'grListId')
            );

        $this->assertFalse($this->sut->getEcommerceSettings()->isEnabled());
        $this->assertTrue($this->sut->getEcommerceSettings()->isEnabled());
    }
}
