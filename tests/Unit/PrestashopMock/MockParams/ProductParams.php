<?php

/**
 * Class ProductParams
 */
class ProductParams
{
    private static $productParams = [
        1 => [
            'id' => 1,
            'name' => 'Tshirt with getResponse logo.',
            'reference' => 'this is sku number',
            'description_short' => 'Product short description',
            'price' => 5.3,
            'price_tax' => 8.0,
            'link_rewrite' => 'link_rewrite',
            'images' => [['id_image' => '1', 'position' => 1], ['id_image' => '2', 'position' => 2]],
            'categories' => [
                ['name' => 'categoryName10', 'id' => '10', 'id_parent' => '1'],
                ['name' => 'categoryName11', 'id' => '11', 'id_parent' => '2']
            ]
        ],
        4 => [
            'id' => 4,
            'name' => 'Tshirt with getResponse logo4.',
            'reference' => 'this is sku number4',
            'description_short' => 'Product short description4',
            'price' => 5.3,
            'price_tax' => 8.0,
            'link_rewrite' => 'link_rewrite',
            'images' => [['id_image' => '1', 'position' => 1], ['id_image' => '2', 'position' => 2]],
            'categories' => [
                ['name' => 'categoryName10', 'id' => '10', 'id_parent' => '1'],
                ['name' => 'categoryName11', 'id' => '11', 'id_parent' => '2']
            ]
        ],
        6 => [
            'id' => 6,
            'name' => 'Tshirt with getResponse logo6.',
            'reference' => '',
            'description_short' => 'Product short description6',
            'price' => 5.3,
            'price_tax' => 8.0,
            'link_rewrite' => 'link_rewrite',
            'images' => [['id_image' => '1', 'position' => 1], ['id_image' => '2', 'position' => 2]],
            'categories' => [
                ['name' => 'categoryName10', 'id' => '10', 'id_parent' => '1'],
                ['name' => 'categoryName11', 'id' => '11', 'id_parent' => '2']
            ]
        ]
    ];

    /**
     * @param int $productId
     * @return array
     */
    public static function createFromId($productId)
    {
        return static::$productParams[$productId];
    }
}