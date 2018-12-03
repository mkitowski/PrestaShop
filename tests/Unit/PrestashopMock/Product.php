<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *
 * Class Product
 */

class Product
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $reference;

    /** @var string */
    public $description_short;

    /** @var string */
    public $link_rewrite;

    /** @var float */
    private $price;

    /** @var float */
    private $price_tax;

    /** @var array */
    private $images;

    /** @var array */
    private $categories;

    public function __construct($id)
    {
        $param = \ProductGenerator::genProductParams($id);

        $this->id = isset($param['id']) ? $param['id'] : null;
        $this->name = isset($param['name'])? $param['name'] : null;
        $this->reference = isset($param['reference']) ? $param['reference'] : null;
        $this->description_short = isset($param['description_short']) ? $param['description_short'] : null;
        $this->price = isset($param['price']) ? $param['price'] : null;
        $this->price_tax = isset($param['price_tax']) ? $param['price_tax'] : null;
        $this->link_rewrite = isset($param['link_rewrite']) ? $param['link_rewrite'] : null;
        $this->images = isset($param['images']) ? $param['images'] : [];
        $this->categories = isset($param['categories']) ? $param['categories'] : [];
    }

    /**
     * @param bool $param
     * @return float
     */
    public function getPrice($param = true)
    {
        return $param ? $this->price_tax : $this->price;
    }

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }
}
