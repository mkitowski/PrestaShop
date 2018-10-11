<?php
namespace GetResponse\Product;

use GrShareCode\Product\Variant\Images\Image;
use GrShareCode\Product\Variant\Images\ImagesCollection;
use Link;
use Tools;

/**
 * Class ProductImagesFactory
 * @package GetResponse\Product
 */
class ProductImagesFactory
{

    /**
     * @param array $productImages
     * @param string $productLinkRewrite
     * @return ImagesCollection
     */
    public function createFromImages(array $productImages, $productLinkRewrite)
    {
        $imagesCollection = new ImagesCollection();

        foreach ($productImages as $productImage) {
            $imagePath = (new Link())->getImageLink($productLinkRewrite, $productImage['id_image'], 'home_default');
            $protocol = Tools::getProtocol(Tools::usingSecureMode());
            $imagesCollection->add(new Image($protocol . $imagePath, (int)$productImage['position']));
        }

        return $imagesCollection;
    }
}