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
 * Class Address
 */

class Address
{
    /** @var int */
    public $id_country;

    /** @var string */
    public $country;

    /** @var string */
    public $firstname;

    /** @var string */
    public $lastname;

    /** @var string */
    public $address1;

    /** @var string */
    public $address2;

    /** @var string */
    public $city;

    /** @var string */
    public $postcode;

    /** @var string */
    public $phone;

    /** @var string */
    public $company;

    /**
     * @param int $addressId
     */
    public function __construct($addressId)
    {
        $this->id_country = 4;
        $this->country = 'Poland';
        $this->firstname = 'Adam';
        $this->lastname = 'Kowalski';
        $this->address1 = 'Arkońska 24';
        $this->address2 = 'Building 5';
        $this->city = 'Gdańsk';
        $this->postcode = '81-190';
        $this->phone = '123-123-123';
        $this->company = 'GetResponse';
    }
}
