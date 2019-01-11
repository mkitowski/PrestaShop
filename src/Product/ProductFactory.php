<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace GetResponse\Product;

use GrShareCode\Product\Product as GrProduct;
use GrShareCode\Product\Variant\VariantsCollection;
use Link;
use Product;

/**
 * Class ProductService
 */
class ProductFactory
{
    /**
     * @param Product $product
     * @param $quantity
     * @return GrProduct
     */
    public function createShareCodeProductFromProduct(Product $product, $quantity)
    {
        $categoryCollection = (new ProductCategoryCollectionFactory)->createFromCategories($product->getCategories());
        $imagesCollection = (new ProductImagesFactory)->createFromImages(
            $product->getImages(null),
            $this->normalizeToString($product->link_rewrite)
        );

        $variant = (new ProductVariantFactory)->createFromProduct($product, $imagesCollection, $quantity);
        $variantCollection = new VariantsCollection();
        $variantCollection->add($variant);

        $grProduct = new GrProduct(
            (int)$product->id,
            $this->normalizeToString($product->name),
            $variantCollection,
            $categoryCollection
        );

        $grProduct
            ->setUrl((new Link())->getProductLink($product));

        return $grProduct;
    }

    /**
     * @param string $text
     * @return mixed
     */
    private function normalizeToString($text)
    {
        return is_array($text) ? reset($text) : $text;
    }
}
