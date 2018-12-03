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

namespace GetResponse\CustomFields;

/**
 * Class DefaultCustomFields
 * @package GetResponse\CustomFields
 */
class DefaultCustomFields
{
    const DEFAULT_CUSTOM_FIELDS = [
        [
            "id" => 1,
            "custom_name" => "firstname",
            "customer_property_name" => "firstName",
            "gr_custom_id" => "",
            "is_active" => true,
            "is_default" => true
        ],[
            "id" => 2,
            "custom_name" => "lastname",
            "customer_property_name" => "lastName",
            "gr_custom_id" => "",
            "is_active" => true,
            "is_default" => true
        ],[
            "id" => 3,
            "custom_name" => "email",
            "customer_property_name" => "email",
            "gr_custom_id" => "",
            "is_active" => true,
            "is_default" => true
        ],[
            "id" => 4,
            "custom_name" => "address",
            "customer_property_name" => "address",
            "gr_custom_id" => "",
            "is_active" => false,
            "is_default" => false
        ],[
            "id" => 5,
            "custom_name" => "postal",
            "customer_property_name" => "postalCode",
            "gr_custom_id" => "",
            "is_active" => false,
            "is_default" => false
        ],[
            "id" => 6,
            "custom_name" => "city",
            "customer_property_name" => "city",
            "gr_custom_id" => "",
            "is_active" => false,
            "is_default" => false
        ],[
            "id" => 7,
            "custom_name" => "phone",
            "customer_property_name" => "phone",
            "gr_custom_id" => "",
            "is_active" => false,
            "is_default" => false
        ],[
            "id" => 8,
            "custom_name" => "country",
            "customer_property_name" => "country",
            "gr_custom_id" => "",
            "is_active" => false,
            "is_default" => false
        ],[
            "id" => 9,
            "custom_name" => "birthday",
            "customer_property_name" => "birthDate",
            "gr_custom_id" => "",
            "is_active" => false,
            "is_default" => false
        ],[
            "id" => 10,
            "custom_name" => "company",
            "customer_property_name" => "company",
            "gr_custom_id" => "",
            "is_active" => false,
            "is_default" => false
        ]
    ];
}
