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

    /**
     * @test
     */
    public function shouldCreateProductFromPrestashopProduct()
    {
        $productParams = [
            'id' => 1,
            'name' => 'Tshirt with getResponse logo.',
            'reference' => 'this is sku number',
            'description_short' => 'Product short description',
            'price' => 5.3,
            'price_tax' => 8.0,
            'link_rewrite' => 'link_rewrite',
            'images' => [['id_image' => '1', 'position' => 1], ['id_image' => '2', 'position' => 2]],
            'categories' => [
                ['name' => 'categoryName10', 'id' => '10', 'id_parent' => '1'],
                ['name' => 'categoryName11', 'id' => '11', 'id_parent' => '2']
            ]
        ];

        $product = new Product($productParams);
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

    protected function setUp()
    {
        $this->productService = new ProductService();
    }
}
