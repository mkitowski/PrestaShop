<?php

class ProductGenerator
{

    const PROD_1_WITH_SKU = 1;
    const PROD_2_WITH_SKU = 2;
    const PROD_3_WITHOUT_SKU = 3;

    private static $products = [
        1 => [
            'id' => 1,
            'id_product' => 1,
            'name' => 'skarpetki',
            'reference' => 'sku-skarpetki',
            'description_short' => 'Desc for product skarpetki',
            'price' => 10.0,
            'price_tax' => 12.0,
            'images' => [['id_image' => '1', 'position' => 1], ['id_image' => '2', 'position' => 2]],
            'categories' => [
                ['name' => 'categoryName10', 'id' => '10', 'id_parent' => '1'],
                ['name' => 'categoryName11', 'id' => '11', 'id_parent' => '2']
            ]
        ],
        2 => [
            'id' => 2,
            'id_product' => 2,
            'name' => 'czapka',
            'reference' => 'sku-czapka',
            'description_short' => 'Desc for product czapka',
            'price' => 9.95,
            'price_tax' => 11.0,
            'images' => [['id_image' => '1', 'position' => 1], ['id_image' => '2', 'position' => 2]],
            'categories' => [
                ['name' => 'categoryName10', 'id' => '10', 'id_parent' => '1'],
                ['name' => 'categoryName11', 'id' => '11', 'id_parent' => '2']
            ]
        ],
        3 => [
            'id' => 3,
            'id_product' => 3,
            'name' => 'ball',
            'reference' => '',
            'description_short' => 'Desc for product ball',
            'price' => 31.0,
            'price_tax' => 35.0,
            'images' => [['id_image' => '1', 'position' => 1], ['id_image' => '2', 'position' => 2]],
            'categories' => [
                ['name' => 'categoryName10', 'id' => '10', 'id_parent' => '1'],
                ['name' => 'categoryName11', 'id' => '11', 'id_parent' => '2']
            ]
        ]
    ];


    /**
     * @param int $id
     * @return array
     */
    public static function genProductParams($id)
    {
        return self::$products[$id];
    }

}