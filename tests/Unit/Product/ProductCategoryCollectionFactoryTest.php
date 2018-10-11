<?php
namespace GetResponse\Tests\Unit\Product;

use GetResponse\Product\ProductCategoryCollectionFactory;
use GetResponse\Tests\Unit\BaseTestCase;
use GrShareCode\Product\Category\Category;
use GrShareCode\Product\Category\CategoryCollection;

/**
 * Class ProductCategoryCollectionFactoryTest
 * @package GetResponse\Tests\Unit\Product
 */
class ProductCategoryCollectionFactoryTest extends BaseTestCase
{

    /**
     * @test
     */
    public function createFromCategories()
    {
        $categories = [
            [
                'name' => 'categoryName10',
                'id' => '10',
                'id_parent' => '1',
            ],
            [
                'name' => 'categoryName11',
                'id' => '11',
                'id_parent' => '2',
            ]
        ];

        $productCategoryCollectionFactory = new ProductCategoryCollectionFactory();
        $categoryCollection = $productCategoryCollectionFactory->createFromCategories($categories);

        $expectedCategoryCollection = new CategoryCollection();
        $expectedCategoryCollection->add(
            (new Category($categories[0]['name']))
            ->setParentId($categories[0]['id_parent'])
            ->setUrl('http://my-prestashop.com/category/' .$categories[0]['id'] )
            ->setExternalId($categories[0]['id'])
        );
        $expectedCategoryCollection->add(
            (new Category($categories[1]['name']))
                ->setParentId($categories[1]['id_parent'])
                ->setUrl('http://my-prestashop.com/category/' .$categories[1]['id'] )
                ->setExternalId($categories[1]['id'])
        );

        $this->assertEquals($expectedCategoryCollection, $categoryCollection);
    }
}
