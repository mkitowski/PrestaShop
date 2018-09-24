<?php

namespace GetResponse\Product;

use GrShareCode\Product\Category\Category as GrCategory;
use GrShareCode\Product\Category\CategoryCollection;
use GrShareCode\Product\Product as GrProduct;
use GrShareCode\Product\Variant\Images\Image;
use GrShareCode\Product\Variant\Images\ImagesCollection;
use GrShareCode\Product\Variant\Variant;
use GrShareCode\Product\Variant\VariantsCollection;
use Product;
use Tools;
use Link;
use Category;

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
        $imagesCollection = new ImagesCollection();
        $categoryCollection = new CategoryCollection();
        $categories = $product->getCategories();

        foreach ($product->getImages(null) as $image) {
            $imagePath = (new Link())->getImageLink($this->normalizeToString($product->link_rewrite), $image['id_image'], 'home_default');
            $imagesCollection->add(new Image(Tools::getProtocol(Tools::usingSecureMode()) . $imagePath, (int)$image['position']));
        }

        foreach ($categories as $category) {

            $prestashopCategory = new Category($category);

            $grCategory = new GrCategory($prestashopCategory->getName());
            $grCategory
                ->setUrl((new Link())->getCategoryLink($prestashopCategory->id))
                ->setExternalId((string)$prestashopCategory->id)
                ->setParentId((string)$prestashopCategory->id_parent);

            $categoryCollection->add($grCategory);
        }

        $grVariant = new Variant(
            $product->id,
            $this->normalizeToString($product->name),
            $product->getPrice(false),
            $product->getPrice(),
            $product->reference
        );

        $grVariant
            ->setQuantity($quantity)
            ->setImages($imagesCollection)
            ->setUrl((new Link())->getProductLink($product))
            ->setDescription($this->normalizeToString($product->description_short));

        $variantCollection = new VariantsCollection();
        $variantCollection->add($grVariant);

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