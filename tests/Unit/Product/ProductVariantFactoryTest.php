<?php
namespace GetResponse\Tests\Unit\Product;

use GetResponse\Product\ProductVariantFactory;
use GetResponse\Tests\Unit\BaseTestCase;
use GrShareCode\Product\Variant\Images\Image;
use GrShareCode\Product\Variant\Images\ImagesCollection;
use GrShareCode\Product\Variant\Variant;
use Product;

/**
 * Class ProductVariantFactoryTest
 * @package GetResponse\Tests\Unit\Product
 */
class ProductVariantFactoryTest extends BaseTestCase
{

    /** @var ProductVariantFactory */
    private $productVariantFactory;

    /**
     * @test
     */
    public function shouldCreateProduct()
    {
        $productParams = \ProductGenerator::genProductParams(\ProductGenerator::PROD_1_WITH_SKU);
        $product = new Product(\ProductGenerator::PROD_1_WITH_SKU);

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

    /**
     * @test
     */
    public function shouldCreateProductWithoutShortDescription()
    {
        $productParams = \ProductGenerator::genProductParams(\ProductGenerator::PROD_3_WITHOUT_SHORT_DESCRIPTION);
        $product = new Product(\ProductGenerator::PROD_3_WITHOUT_SHORT_DESCRIPTION);

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
            ->setUrl('http://my-prestashop.com/product/' . $productParams['id']);

        $this->assertEquals($expectedVariant, $variant);

    }

    protected function setUp()
    {
        $this->productVariantFactory = new ProductVariantFactory();
    }
}
