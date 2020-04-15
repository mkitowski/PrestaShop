<?php
/**
 * 2007-2020 PrestaShop
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
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

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
            ->setUrl('http://my-prestashop.com/category/' .$categories[0]['id'])
            ->setExternalId($categories[0]['id'])
        );
        $expectedCategoryCollection->add(
            (new Category($categories[1]['name']))
                ->setParentId($categories[1]['id_parent'])
                ->setUrl('http://my-prestashop.com/category/' .$categories[1]['id'])
                ->setExternalId($categories[1]['id'])
        );

        $this->assertEquals($expectedCategoryCollection, $categoryCollection);
    }
}
