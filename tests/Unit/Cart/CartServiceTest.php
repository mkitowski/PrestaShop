<?php
namespace GetResponse\Tests\Unit\Cart;

use Cart;
use GetResponse\Cart\CartService;
use GetResponse\Product\ProductService;
use GetResponse\Tests\Unit\BaseTestCase;
use GrShareCode\Cart\CartService as GrCartService;
use GrShareCode\Cart\AddCartCommand as GrAddCartCommand;
use GrShareCode\Cart\Cart as GrCart;
use GrShareCode\Product\ProductsCollection;
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

    protected function setUp()
    {
        $this->grCartService = $this->getMockWithoutConstructing(GrCartService::class);
        $this->sut = new CartService($this->grCartService);
    }

    /**
     * @test
     */
    public function shouldRemoveCartWhenNoProducts()
    {
        $params = [
            'id' => 'grId',
            'id_currency' => 1,
            'id_customer' => 1,
            'total_with_tax' => (float)10,
            'total' => (float)8,
            'products' => []
        ];

        $contactListId = 'contactListId';
        $grShopId = 'grShopId';

        $cart = new Cart($params);

        $grCart = new GrCart(
            $params['id'],
            new ProductsCollection(),
            'PLN',
            $params['total'],
            $params['total_with_tax']
        );

        $grAddCartCommand = new GrAddCartCommand($grCart, 'customer@getresponse.com', $contactListId, $grShopId);

        $this->grCartService
            ->expects(self::once())
            ->method('sendCart')
            ->with($grAddCartCommand);


        $this->sut->sendCart($cart, $contactListId, $grShopId);
    }


    /**
     * @test
     */
    public function shouldSkipProductsWithEmptySku()
    {
        $product1 = \ProductGenerator::genProductParams(\ProductGenerator::PROD_1_WITH_SKU);
        $product1['quantity'] = 1;

        $product2 = \ProductGenerator::genProductParams(\ProductGenerator::PROD_3_WITHOUT_SKU);
        $product2['quantity'] = 2;

        $params = [
            'id' => 'grId',
            'id_currency' => 1,
            'id_customer' => 1,
            'total_with_tax' => (float)10,
            'total' => (float)8,
            'products' => [$product1, $product2]
        ];

        $contactListId = 'contactListId';
        $grShopId = 'grShopId';

        $cart = new Cart($params);

        $getresponseProduct = (new ProductService())->createProductFromPrestaShopProduct(new \Product(\ProductGenerator::PROD_1_WITH_SKU), 1);
        $productsCollection = new ProductsCollection();
        $productsCollection->add($getresponseProduct);


        $grCart = new GrCart(
            $params['id'],
            $productsCollection,
            'PLN',
            $params['total'],
            $params['total_with_tax']
        );

        $grAddCartCommand = new GrAddCartCommand($grCart, 'customer@getresponse.com', $contactListId, $grShopId);

        $this->grCartService
            ->expects(self::once())
            ->method('sendCart')
            ->with($grAddCartCommand);


        $this->sut->sendCart($cart, $contactListId, $grShopId);
    }


    /**
     * @test
     */
    public function shouldSendCart()
    {
        $product1 = \ProductGenerator::genProductParams(\ProductGenerator::PROD_1_WITH_SKU);
        $product1['quantity'] = 1;

        $product2 = \ProductGenerator::genProductParams(\ProductGenerator::PROD_2_WITH_SKU);
        $product2['quantity'] = 2;

        $params = [
            'id' => 'grId',
            'id_currency' => 1,
            'id_customer' => 1,
            'total_with_tax' => 10.0,
            'total' => 8.0,
            'products' => [$product1, $product2]
        ];

        $contactListId = 'contactListId';
        $grShopId = 'grShopId';

        $cart = new Cart($params);

        $productsCollection = new ProductsCollection();
        $productsCollection->add((new ProductService())->createProductFromPrestaShopProduct(new \Product(\ProductGenerator::PROD_1_WITH_SKU), 1));
        $productsCollection->add((new ProductService())->createProductFromPrestaShopProduct(new \Product(\ProductGenerator::PROD_2_WITH_SKU), 2));

        $grCart = new GrCart(
            $params['id'],
            $productsCollection,
            'PLN',
            $params['total'],
            $params['total_with_tax']
        );

        $grAddCartCommand = new GrAddCartCommand($grCart, 'customer@getresponse.com', $contactListId, $grShopId);

        $this->grCartService
            ->expects(self::once())
            ->method('sendCart')
            ->with($grAddCartCommand);

        $this->sut->sendCart($cart, $contactListId, $grShopId);
    }
}
