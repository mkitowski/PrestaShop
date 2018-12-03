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

namespace GetResponse\Account;

use Configuration;

/**
 * Class AccountRepository
 * @package GetResponse\Account
 */
class AccountRepository
{
    const INVALID_REQUEST = 'getresponse_invalid_request_date';
    const ORIGIN_CUSTOM_FIELD = 'getresponse_origin_custom_field';

    /**
     * @return string
     */
    public function getInvalidRequestDate()
    {
        return json_decode(Configuration::get(self::INVALID_REQUEST), true);
    }

    /**
     * @param string $date
     */
    public function updateInvalidRequestDate($date)
    {
        Configuration::updateValue(self::INVALID_REQUEST, $date);
    }

    public function clearInvalidRequestDate()
    {
        Configuration::updateValue(self::INVALID_REQUEST, null);
    }

    /**
     * @return string
     */
    public function getOriginCustomFieldValue()
    {
        return Configuration::get(self::ORIGIN_CUSTOM_FIELD);
    }

    /**
     * @param string $id
     */
    public function updateOriginCustomFieldId($id)
    {
        Configuration::updateValue(self::ORIGIN_CUSTOM_FIELD, $id);
    }

    public function clearOriginCustomFieldId()
    {
        Configuration::updateValue(self::ORIGIN_CUSTOM_FIELD, null);
    }
}
