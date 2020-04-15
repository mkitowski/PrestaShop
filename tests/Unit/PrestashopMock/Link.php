<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author     Getresponse <grintegrations@getresponse.com>
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *
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
