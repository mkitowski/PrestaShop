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
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace GetResponse\Product;

use GrShareCode\Product\Variant\Images\ImagesCollection;
use GrShareCode\Product\Variant\Variant;
use Link;
use Product;
use Tools;

/**
 * Class ProductVariantFactory
 * @package GetResponse\Product
 */
class ProductVariantFactory
{
    const VARIANT_DESC_MAX_LENGTH = 1000;

    /**
     * @param Product $product
     * @param ImagesCollection $imagesCollection
     * @param int $quantity
     * @return Variant
     */
    public function createFromProduct(Product $product, ImagesCollection $imagesCollection, $quantity)
    {
        $variant = new Variant(
            $product->id,
            $this->normalizeToString($product->name),
            $product->getPrice(false),
            $product->getPrice(),
            $product->reference
        );

        $variant
            ->setQuantity($quantity)
            ->setImages($imagesCollection)
            ->setUrl((new Link())->getProductLink($product))
            ->setDescription($this->getDescription($product));

        return $variant;
    }

    /**
     * @param string $text
     * @return mixed
     */
    private function normalizeToString($text)
    {
        return is_array($text) ? reset($text) : $text;
    }

    /**
     * @param Product $product
     * @return bool|string
     */
    private function getDescription(Product $product)
    {
        $description = $this->normalizeToString($product->description_short);

        if (empty($description)) {
            return null;
        }

        return Tools::substr($description, 0, self::VARIANT_DESC_MAX_LENGTH);
    }
}
