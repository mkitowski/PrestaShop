<?php
namespace GetResponse\Tests\Unit\Cart;

use Cart;
use GetResponse\Cart\CartService;
use GetResponse\Tests\Unit\BaseTestCase;
use GrShareCode\Cart\CartService as GrCartService;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class CartServiceTest
 * @package GetResponse\Tests\Unit\Cart
 */
class CartServiceTest extends BaseTestCase
{
    /** @var GrCartService | PHPUnit_Framework_MockObject_MockObject */
    private $grCartService;

    /** @var CartService */
    private $sut;

    /**
     * @test
     */
    public function shouldNotSendCartForEmptyCart()
    {
        $params = ['products' => []];

        $contactListId = 'contactListId';
        $grShopId = 'grShopId';

        $cart = new Cart($params);
        $this->sut->sendCart($cart, $contactListId, $grShopId);

        $this->grCartService
            ->expects(self::never())
            ->method('sendCart');
    }


    /**
     * @test
     */
    public function shouldNotSendCartIfProductSkuIsEmpty()
    {
        $params = [
            'products' => [
                [
                    'id_product' => 6,
                    'quantity' => 7,
                ]
            ]
        ];

        $contactListId = 'contactListId';
        $grShopId = 'grShopId';

        $cart = new Cart($params);
        $this->sut->sendCart($cart, $contactListId, $grShopId);

        $this->grCartService
            ->expects(self::never())
            ->method('sendCart');
    }


    /**
     * @test
     */
    public function shouldSendCart()
    {
        $params = [
            'id' => '34',
            'id_currency' => '3',
            'total' => 22.56,
            'total_with_tax' => 25.00,
            'id_customer' => 1,
            'products' => [
                [
                    'id_product' => 1,
                    'quantity' => 2,
                ],
                [
                    'id_product' => 4,
                    'quantity' => 1,
                ]
            ]
        ];

        $contactListId = 'contactListId';
        $grShopId = 'grShopId';

        $this->grCartService
            ->expects(self::once())
            ->method('sendCart');

        $this->sut->sendCart(new Cart($params), $contactListId, $grShopId);
    }

    protected function setUp()
    {
        $this->grCartService = $this->getMockWithoutConstructing(GrCartService::class);
        $this->sut = new CartService($this->grCartService);
    }
}
