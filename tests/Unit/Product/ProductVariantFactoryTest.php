<?php
namespace GetResponse\Tests\Unit\Product;

use GetResponse\Product\ProductVariantFactory;
use GetResponse\Tests\Unit\BaseTestCase;
use GrShareCode\Product\Variant\Images\Image;
use GrShareCode\Product\Variant\Images\ImagesCollection;
use GrShareCode\Product\Variant\Variant;
use Product;

class ProductVariantFactoryTest extends BaseTestCase
{

    /** @var ProductVariantFactory */
    private $productVariantFactory;

    /**
     * @test
     */
    public function shouldCreateProduct()
    {
        $productParams = [
            'id' => 1,
            'name' => 'Tshirt with getResponse logo.',
            'reference' => 'this is sku number',
            'description_short' => 'Product short description',
            'price' => 5.3,
            'price_tax' => 8.0
        ];
        $product = new Product($productParams);

        $imagesCollection = new ImagesCollection();
        $imagesCollection->add(new Image('source1', 1));
        $imagesCollection->add(new Image('source2', 2));
        $quantity = 2;

        $variant = $this->productVariantFactory->createFromProduct($product, $imagesCollection, $quantity);

        $expectedVariant = new Variant(
            $productParams['id'],
            $productParams['name'],
            $productParams['price'],
            $productParams['price_tax'],
            $productParams['reference']
        );

        $expectedVariant
            ->setQuantity($quantity)
            ->setImages($imagesCollection)
            ->setUrl('http://my-prestashop.com/product/' . $productParams['id'])
            ->setDescription($productParams['description_short']);

        $this->assertEquals($expectedVariant, $variant);

    }

    protected function setUp()
    {
        $this->productVariantFactory = new ProductVariantFactory();
    }
}
