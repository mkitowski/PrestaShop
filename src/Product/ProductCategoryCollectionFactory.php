<?php
namespace GetResponse\Product;

use Category;
use GrShareCode\Product\Category\Category as GrCategory;
use GrShareCode\Product\Category\CategoryCollection;
use Link;
use Product;

/**
 * Class ProductCategoryCollection
 * @package GetResponse\Product
 */
class ProductCategoryCollectionFactory
{
    /**
     * @param array $categories
     * @return CategoryCollection
     */
    public function createFromCategories($categories)
    {
        $categoryCollection = new CategoryCollection();

        foreach ($categories as $category) {

            $productCategory = new Category($category);

            $grCategory = new GrCategory($productCategory->getName());
            $grCategory
                ->setUrl((new Link())->getCategoryLink($productCategory->id))
                ->setExternalId((string)$productCategory->id)
                ->setParentId((string)$productCategory->id_parent);

            $categoryCollection->add($grCategory);
        }

        return $categoryCollection;
    }
}