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

namespace GetResponse\Ecommerce;

use Configuration;

/**
 * Class EcommerceRepository
 */
class EcommerceRepository
{
    const RESOURCE_KEY = 'getresponse_ecommerce';

    /**
     * @return Ecommerce
     */
    public function getEcommerceSettings()
    {
        $result = json_decode(Configuration::get(self::RESOURCE_KEY), true);

        if (empty($result)) {
            return new Ecommerce(Ecommerce::STATUS_INACTIVE, null, null);
        }

        return new Ecommerce($result['status'], $result['shop_id'], $result['list_id']);
    }

    /**
     * @param Ecommerce $settings
     */
    public function updateEcommerceSubscription(Ecommerce $settings)
    {
        Configuration::updateValue(
            self::RESOURCE_KEY,
            json_encode([
                'status' => $settings->getStatus(),
                'shop_id' => $settings->getShopId(),
                'list_id' => $settings->getListId()
            ])
        );
    }

    public function clearEcommerceSettings()
    {
        Configuration::updateValue(self::RESOURCE_KEY, null);
    }
}
