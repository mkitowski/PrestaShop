<?php
namespace GetResponse\Tests\Unit\Product;

use GetResponse\Product\ProductImagesFactory;
use GetResponse\Product\ProductService;
use GetResponse\Product\ProductVariantFactory;
use GetResponse\Tests\Unit\BaseTestCase;
use Product;

/**
 * Class ProductServiceTest
 * @package GetResponse\Tests\Unit\Product
 */
class ProductServiceTest extends BaseTestCase
{

    /** @var ProductService */
    private $productService;

    protected function setUp()
    {
        $this->productService = new ProductService();
    }

    /**
     * @test
     */
    public function shouldCreateProductFromPrestashopProduct()
    {
        $productParams = \ProductGenerator::genProductParams(\ProductGenerator::PROD_1_WITH_SKU);

        $product = new Product(\ProductGenerator::PROD_1_WITH_SKU);
        $quantity = 2;

        $grProduct = $this->productService->createProductFromPrestaShopProduct($product, $quantity);

        $this->assertEquals($productParams['id'], $grProduct->getExternalId());
        $this->assertEquals($productParams['name'], $grProduct->getName());
        $this->assertEquals('http://my-prestashop.com/product/' . $productParams['id'], $grProduct->getUrl());

        $imagesCollection = (new ProductImagesFactory)->createFromImages(
            $product->getImages(null),
            $product->link_rewrite
        );
        $variantProduct = (new ProductVariantFactory)->createFromProduct($product, $imagesCollection, $quantity);
        $this->assertEquals($variantProduct, $grProduct->getVariants()->getIterator()->current());

    }
}
