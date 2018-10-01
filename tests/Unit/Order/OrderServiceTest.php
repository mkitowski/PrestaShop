<?php
namespace GetResponse\Tests\Unit\Order;

use GetResponse\Order\OrderService;
use GetResponse\Tests\Unit\BaseTestCase;
use GrShareCode\Order\OrderService as GrOrderService;
use Order;
use PHPUnit_Framework_MockObject_MockObject;

class OrderServiceTest extends BaseTestCase
{

    /** @var GrOrderService | PHPUnit_Framework_MockObject_MockObject */
    private $grOrderService;

    /** @var OrderService */
    private $sut;

    /**
     * @test
     */
    public function shouldNotSendOrderIfNoProductFounded()
    {
        $order = new Order([
            'products' => [],
            'id'
        ]);

        $contactListId = 'contactListId';
        $grShopId = 'grShopId';

        $this->sut->sendOrder($order, $contactListId, $grShopId);

        $this->grOrderService
            ->expects(self::never())
            ->method('sendOrder');
    }

//    /**
//     * @test
//     */
//    public function shouldSendOrderIfNoProductFounded()
//    {
//        $order = new Order([
//            'products' => [
//                [
//                    'id_product' => 1,
//                    'product_quantity' => 2,
//                ],
//                [
//                    'id_product' => 4,
//                    'product_quantity' => 1,
//                ]
//            ],
//            'id' => 'id',
//            'current_state' => 'pending',
//            'date_add' => '2018-10-10 12:12:12'
//        ]);
//
//        $contactListId = 'contactListId';
//        $grShopId = 'grShopId';
//
//        $this->grOrderService
//            ->expects(self::once())
//            ->method('sendOrder');
//
//        $this->sut->sendOrder($order, $contactListId, $grShopId);
//
//    }

    protected function setUp()
    {
        $this->grOrderService = $this->getMockWithoutConstructing(GrOrderService::class);
        $this->sut = new OrderService($this->grOrderService);
    }
}
