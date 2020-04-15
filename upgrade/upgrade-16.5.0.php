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
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_16_5_0($object)
{
    update_customs();
    return true;
}


function update_customs()
{
    $raw_customs = Configuration::get('getresponse_customs');

    if (empty($raw_customs)) {
        Configuration::updateValue(
            'getresponse_customs',
            json_encode([])
        );
        return;
    }

    $old_customs = json_decode($raw_customs, true);
    $new_customs = [];

    foreach ($old_customs as $custom) {
        if (1 == $custom['is_active'] && 1 != $custom['is_default']) {
            $new_customs[] = [
                'customer_property_name' => $custom['customer_property_name'],
                'gr_custom_id' => $custom['gr_custom_id'],
            ];
        }
    }

    Configuration::updateValue(
        'getresponse_customs',
        json_encode($new_customs)
    );
}
