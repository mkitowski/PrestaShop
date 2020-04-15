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
 * Class Cart
 */

class Cart
{
    /** @var string */
    public $id;

    /** @var int */
    public $id_currency;

    /** @var int */
    public $id_customer;

    /** @var array */
    private $products;

    /** @var float */
    private $total;

    /** @var float */
    private $total_with_tax;

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->products = $params['products'];
        $this->id = isset($params['id']) ? $params['id'] : null;
        $this->total = isset($params['total']) ? (float)$params['total'] : (float)0;
        $this->total_with_tax = isset($params['total_with_tax']) ? (float)$params['total_with_tax'] : (float)0;
        $this->id_currency = isset($params['id_currency']) ? $params['id_currency'] : null;
        $this->id_customer = isset($params['id_customer']) ? $params['id_customer'] : null;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param bool $taxIncluded
     * @return float
     */
    public function getOrderTotal($taxIncluded)
    {
        return $taxIncluded ? $this->total_with_tax : $this->total;
    }
}
