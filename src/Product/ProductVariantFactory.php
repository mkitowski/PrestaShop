<?php
namespace GetResponse\Product;

use GrShareCode\Product\Variant\Images\ImagesCollection;
use GrShareCode\Product\Variant\Variant;
use Link;
use Product;

/**
 * Class ProductVariantFactory
 * @package GetResponse\Product
 */
class ProductVariantFactory
{
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
            ->setDescription($this->normalizeToString($product->description_short));

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

}