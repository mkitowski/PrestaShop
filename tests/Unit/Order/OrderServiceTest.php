<?php
namespace GetResponse\Tests\Unit\Order;

use GetResponse\Order\OrderService;
use GetResponse\Product\ProductService;
use GetResponse\Tests\Unit\BaseTestCase;
use GrShareCode\Address\Address;
use GrShareCode\Order\AddOrderCommand;
use GrShareCode\Order\Order as GrOrder;
use GrShareCode\Order\OrderService as GrOrderService;
use GrShareCode\Product\Category\Category;
use GrShareCode\Product\Category\CategoryCollection;
use GrShareCode\Product\Product;
use GrShareCode\Product\ProductsCollection;
use GrShareCode\Product\Variant\Images\Image;
use GrShareCode\Product\Variant\Images\ImagesCollection;
use GrShareCode\Product\Variant\Variant;
use GrShareCode\Product\Variant\VariantsCollection;
use Order;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class OrderServiceTest
 * @package GetResponse\Tests\Unit\Order
 */
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
        ]);

        $contactListId = 'contactListId';
        $grShopId = 'grShopId';

        $this->grOrderService
            ->expects(self::never())
            ->method('sendOrder');

        $this->sut->sendOrder($order, $contactListId, $grShopId);
    }

    /**
     * @test
     */
    public function shouldNotSendOrderIfProductSkuIsEmpty()
    {
        $product = \ProductGenerator::genProductParams(\ProductGenerator::PROD_3_WITHOUT_SKU);
        $product['product_quantity'] = 5;

        $order = new Order(['products' => [$product]]);

        $contactListId = 'contactListId';
        $grShopId = 'grShopId';

        $this->grOrderService
            ->expects(self::never())
            ->method('sendOrder');

        $this->sut->sendOrder($order, $contactListId, $grShopId);
    }

    /**
     * @test
     */
    public function shouldSendOrder()
    {
        $product1 = \ProductGenerator::genProductParams(\ProductGenerator::PROD_1_WITH_SKU);
        $product1['product_quantity'] = 2;

        $product2 = \ProductGenerator::genProductParams(\ProductGenerator::PROD_2_WITH_SKU);
        $product2['product_quantity'] = 1;

        $productService = new ProductService();
        $productsCollection = new ProductsCollection();
        $productsCollection->add(
            $productService->createProductFromPrestaShopProduct(new \Product(\ProductGenerator::PROD_1_WITH_SKU), 2)
        );
        $productsCollection->add(
            $productService->createProductFromPrestaShopProduct(new \Product(\ProductGenerator::PROD_2_WITH_SKU), 1)
        );

        $params = [
            'products' => [$product1, $product2],
            'id' => 'id',
            'current_state' => 'pending',
            'date_add' => '2018-10-10 12:12:12',
            'id_address_delivery' => 5,
            'id_cart' => 5,
            'id_customer' => 1,
            'total_paid_tax_excl' => '12',
            'total_paid_tax_incl' => '14'
        ];

        $contactListId = 'contactListId';
        $grShopId = 'grShopId';

        $order = new GrOrder(
            $params['id'],
            $productsCollection,
            12.0,
            2.0,
            'http://my-prestashop.com/?controller=order-detail&id_order=id',
            'PLN',
            'pending',
            '5',
            '',
            0.0,
            'pending',
            '2018-10-10T12:12:12+0000',
            $this->getAddress(),
            $this->getAddress()
        );

        $expected = new AddOrderCommand(
            $order,
            'customer@getresponse.com',
            'contactListId',
            'grShopId',
            false
        );

        $this->grOrderService
            ->expects(self::once())
            ->method('sendOrder')
            ->with($expected);

        $this->sut->sendOrder(new Order($params), $contactListId, $grShopId);

    }

    /**
     * @return CategoryCollection
     */
    private function getCategoriesCollection()
    {
        $categoryCollection = new CategoryCollection();
        $categoryCollection->add(
            (new Category('categoryName10'))
                ->setParentId('1')
                ->setExternalId('10')
                ->setUrl('http://my-prestashop.com/category/10')
        );
        $categoryCollection->add(
            (new Category('categoryName11'))
                ->setParentId('2')
                ->setExternalId('11')
                ->setUrl('http://my-prestashop.com/category/11')
        );

        return $categoryCollection;
    }

    protected function setUp()
    {
        $this->grOrderService = $this->getMockWithoutConstructing(GrOrderService::class);
        $this->sut = new OrderService($this->grOrderService);
    }

    /**
     * @return VariantsCollection
     */
    private function getVariantsCollection1()
    {
        $variantsCollection = new VariantsCollection();
        $variantsCollection->add(
            (new Variant(
                1,
                'Tshirt with getResponse logo.',
                5.3,
                8.0,
                'this is sku number'
            ))->setQuantity(2)
            ->setUrl('http://my-prestashop.com/product/1')
            ->setDescription('Product short description')
            ->setImages($this->getImagesCollection())
        );

        return $variantsCollection;
    }

    /**
     * @return VariantsCollection
     */
    private function getVariantsCollection2()
    {
        $variantsCollection = new VariantsCollection();
        $variantsCollection->add(
            (new Variant(
                4,
                'Tshirt with getResponse logo4.',
                5.3,
                8.0,
                'this is sku number4'
            ))->setQuantity(1)
                ->setUrl('http://my-prestashop.com/product/4')
                ->setDescription('Product short description4')
                ->setImages($this->getImagesCollection())
        );

        return $variantsCollection;
    }

    /**
     * @return ImagesCollection
     */
    private function getImagesCollection()
    {
        $imagesCollection = new ImagesCollection();
        $imagesCollection->add(
            new Image('http://my-prestashop.com/images/1', 1)
        );
        $imagesCollection->add(
            new Image('http://my-prestashop.com/images/2', 2)
        );

        return $imagesCollection;
    }

    /**
     * @return Address
     */
    private function getAddress()
    {
        $address = new Address('POL', 'Adam Kowalski');
        $address->setCountryName('Poland');
        $address
            ->setFirstName('Adam')
            ->setLastName('Kowalski')
            ->setAddress1('Arkońska 24')
            ->setAddress2('Building 5')
            ->setCity('Gdańsk')
            ->setZip('81-190')
            ->setPhone('123-123-123')
            ->setCompany('GetResponse');

        return $address;
    }
}
