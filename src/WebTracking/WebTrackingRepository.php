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

namespace GetResponse\WebTracking;

use Configuration;

/**
 * Class WebTrackingRepository
 * @package GetResponse\WebTracking
 */
class WebTrackingRepository
{
    const WEB_TRACKING_KEY = 'getresponse_web_tracking';
    const TRACKING_CODE_KEY = 'getresponse_tracking_code';

    /**
     * @return WebTracking|null
     */
    public function getWebTracking()
    {
        $status = json_decode(Configuration::get(self::WEB_TRACKING_KEY), true);

        if (empty($status)) {
            return WebTracking::createEmptyInstance();
        }

        return new WebTracking($status['status'], Configuration::get(self::TRACKING_CODE_KEY));
    }

    /**
     * @param WebTracking $webTracking
     */
    public function updateWebTracking(WebTracking $webTracking)
    {
        Configuration::updateValue(
            self::WEB_TRACKING_KEY,
            json_encode(['status' => $webTracking->getStatus()]),
            true
        );

        Configuration::updateValue(self::TRACKING_CODE_KEY, $webTracking->getSnippet(), true);
    }

    public function clearWebTracking()
    {
        Configuration::deleteByName(self::WEB_TRACKING_KEY);
        Configuration::deleteByName(self::TRACKING_CODE_KEY);
    }
}
