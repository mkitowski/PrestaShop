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
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_16_3_0($object)
{
    return (add_hooks_1630($object) && add_sql_1630($object));
}

function add_sql_1630($object)
{
    $sql = array();

    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'getresponse_jobs` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(32) DEFAULT NULL,
              `content` text,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8;';

    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'getresponse_carts` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `gr_shop_id` varchar(16) DEFAULT NULL,
              `cart_id` int(11) DEFAULT NULL,
              `gr_cart_id` varchar(16) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

    $sql[] = 'CREATE TABLE `' . _DB_PREFIX_ . 'getresponse_orders` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `gr_shop_id` varchar(16) DEFAULT NULL,
              `order_id` int(11) DEFAULT NULL,
              `gr_order_id` varchar(16) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

    $sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'getresponse_products`';

    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'getresponse_products` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `product_id` int(11) DEFAULT NULL,
              `gr_product_id` varchar(16) DEFAULT NULL,
              `gr_shop_id` varchar(16) DEFAULT NULL,
              `gr_variant_id` varchar(16) DEFAULT NULL,
              `variant_id` int(11) DEFAULT NULL,
              `payload_md5` VARCHAR(32) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

    $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'getresponse_settings` ADD `invalid_request_date` DATETIME NULL AFTER `crypto`;
';

    //Install SQL
    foreach ($sql as $s) {
        try {
            Db::getInstance()->Execute($s);
        } catch (Exception $e) {
        }
    }

    return true;
}

function add_hooks_1630($object)
{
    return $object->registerHook('actionCronJob');
}
