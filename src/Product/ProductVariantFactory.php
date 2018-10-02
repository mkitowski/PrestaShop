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
            ->setDescription(substr($this->normalizeToString($product->description_short), 0, self::VARIANT_DESC_MAX_LENGTH));

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