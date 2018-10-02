<?php
namespace GetResponse\Tests\Unit\Product;

use GetResponse\Product\ProductImagesFactory;
use GetResponse\Tests\Unit\BaseTestCase;
use GrShareCode\Product\Variant\Images\Image;
use GrShareCode\Product\Variant\Images\ImagesCollection;

/**
 * Class ProductImagesFactoryTest
 * @package GetResponse\Tests\Unit\Product
 */
class ProductImagesFactoryTest extends BaseTestCase
{

    /**
     * @test
     */
    public function shouldCreateImagesFromProductImages()
    {
        $productLinkRewrite = 'adsad';
        $productImages = [
            [
                'id_image' => '1',
                'position' => 1
            ],
            [
                'id_image' => '2',
                'position' => 2
            ],
        ];

        $productImageFactory = new ProductImagesFactory();
        $imagesCollection = $productImageFactory->createFromImages($productImages, $productLinkRewrite);

        $expectedImagesCollection = new ImagesCollection();
        $expectedImagesCollection->add(
            new Image(
                'http://my-prestashop.com/images/' . $productImages[0]['id_image'],
                $productImages[0]['position']
            )
        );
        $expectedImagesCollection->add(
            new Image(
                'http://my-prestashop.com/images/' . $productImages[1]['id_image'],
                $productImages[1]['position']
            )
        );

        $this->assertEquals($expectedImagesCollection, $imagesCollection);
    }
}
