<?php

namespace GetResponse\Product;

use GrShareCode\Product\Product as GrProduct;
use GrShareCode\Product\Variant\VariantsCollection;
use Link;
use Product;

/**
 * Class ProductService
 */
class ProductService
{
    /**
     * @param Product $product
     * @param int $quantity
     * @return GrProduct
     */
    public function createProductFromPrestaShopProduct(Product $product, $quantity)
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