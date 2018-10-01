<?php

/**
 * Class Link
 */
class Link
{
    const PRESTASHOP_URL = 'http://my-prestashop.com/';

    /**
     * @param int $categoryId
     * @return string
     */
    public function getCategoryLink($categoryId)
    {
        return self::PRESTASHOP_URL . 'category/' . $categoryId;
    }

    /**
     * @param string $protocol
     * @param $imageId
     * @param $string
     * @return string
     */
    public function getImageLink($protocol, $imageId, $string)
    {
        return self::PRESTASHOP_URL . 'images/' . $imageId;
    }

    /**
     * @param Product $product
     * @return string
     */
    public function getProductLink(Product $product)
    {
        return self::PRESTASHOP_URL . 'product/' . $product->id;
    }
}