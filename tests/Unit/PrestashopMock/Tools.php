<?php
/**
 * 2007-2019 PrestaShop
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
 * Class Tools
 */

class Tools
{

    /**
     * @param null $use_ssl
     * @return null
     */
    public static function getProtocol($use_ssl = null)
    {
        return null;
    }

    /**
     * @return null
     */
    public static function usingSecureMode()
    {
        return null;
    }

    /**
     * @param bool $http
     * @param bool $entities
     * @param bool $ignore_port
     * @return null
     */
    public static function getHttpHost($http = false, $entities = false, $ignore_port = false)
    {
        return null;
    }

    /**
     * @param $value
     * @return int
     */
    public static function strlen($value)
    {
        return mb_strlen($value);
    }

    /**
     * @param $str
     * @param $start
     * @param bool $length
     * @param string $encoding
     * @return int
     */
    public static function substr($str, $start, $length = false, $encoding = 'utf-8')
    {
        return mb_substr($str, $start, ($length === false ? Tools::strlen($str) : (int) $length));
    }
}
