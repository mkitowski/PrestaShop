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
 */

namespace GetResponse\Customer;

use \Customer as PsCustomer;

/**
 * Class CustomerFactory
 * @package GetResponse\Customer
 */
class CustomerFactory
{
    /**
     * @param array $params
     * @return Customer
     */
    public static function createFromArray($params)
    {
        return new Customer(
            $params['id'],
            $params['firstname'],
            $params['lastname'],
            $params['birthday'],
            $params['address'],
            $params['postal'],
            $params['company'],
            $params['country'],
            $params['city'],
            $params['phone'],
            $params['email']
        );
    }

    /**
     * @param string $email
     * @return Customer
     */
    public static function createFromNewsletter($email)
    {
        return new Customer(
            0,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $email
        );
    }

    /**
     * @param $customer
     * @return Customer
     */
    public static function createFromPsCustomerObject(PsCustomer $customer)
    {
        return new Customer(
            $customer->id,
            $customer->firstname,
            $customer->lastname,
            $customer->birthday,
            null,
            null,
            null,
            null,
            null,
            null,
            $customer->email
        );
    }
}
