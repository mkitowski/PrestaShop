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

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_16_4_0($object)
{
    $idShop = Context::getContext()->shop->id;
    upgradeCustomsTable();
    upgradeEcommerceTable($idShop);
    upgradeSettingsTable($idShop);
    upgradeWebFormsTable($idShop);
    return true;
}

function upgradeCustomsTable()
{
    $sql = "DROP TABLE IF EXISTS "._DB_PREFIX_."getresponse_customs";
    DB::getInstance()->execute($sql);
}

function upgradeEcommerceTable($idShop)
{
    $sql = "SELECT * FROM "._DB_PREFIX_."getresponse_ecommerce WHERE id_shop = " . $idShop;
    $result = Db::getInstance()->getRow($sql);

    $sql = "SELECT * FROM "._DB_PREFIX_."getresponse_settings WHERE id_shop = " . $idShop;
    $settings = Db::getInstance()->getRow($sql);

    if (!empty($result)) {

        Configuration::updateValue(
            'getresponse_ecommerce',
            json_encode([
                'status' => 'active',
                'shop_id' => isset($result['gr_id_shop']) ? $result['gr_id_shop'] : null,
                'list_id' => isset($settings['campaign_id']) ? $settings['campaign_id'] : null
            ])
        );
    }

    $sql = "DROP TABLE IF EXISTS "._DB_PREFIX_."getresponse_ecommerce";
    DB::getInstance()->execute($sql);
}

function upgradeSettingsTable($idShop)
{
    $sql = "SELECT * FROM "._DB_PREFIX_."getresponse_settings WHERE id_shop = " . $idShop;
    $result = Db::getInstance()->getRow($sql);

    if (!empty($result['api_key'])) {

        Configuration::updateValue(
            'getresponse_account',
            json_encode([
                'api_key' => $result['api_key'],
                'type' => $result['account_type'],
                'domain' => $result['crypto']
            ])
        );

        if (true == $result['active_subscription']) {
            Configuration::updateValue(
                'getresponse_registration',
                json_encode([
                    'active_subscription' => $result['active_subscription'],
                    'active_newsletter_subscription' => $result['active_newsletter_subscription'],
                    'campaign_id' => $result['campaign_id'],
                    'cycle_day' => $result['cycle_day'],
                    'update_address' => $result['update_address'],
                ])
            );
        }

        $status = $result['active_tracking'] === 'yes' ? 'active' : 'inactive';

        Configuration::updateValue(
            'getresponse_web_tracking',
            json_encode(['status' => $status]),
            true
        );

        Configuration::updateValue('getresponse_tracking_code', $result['tracking_snippet'], true);

        if (isset($result['invalid_request_date'])) {
            Configuration::updateValue('getresponse_invalid_request_date', $result['invalid_request_date']);
        }
    }

    $sql = "DROP TABLE IF EXISTS "._DB_PREFIX_."getresponse_settings";
    DB::getInstance()->execute($sql);
}

function upgradeWebFormsTable($idShop)
{
    $sql = "SELECT * FROM "._DB_PREFIX_."getresponse_webform WHERE id_shop = " . $idShop;
    $result = Db::getInstance()->getRow($sql);

    if (!empty($result['webform_id'])) {

        Configuration::updateValue(
            'getresponse_forms',
            json_encode([
                'status' => $result['active_subscription'] === 'yes' ? 'active' : 'inactive',
                'webform_id' => $result['webform_id'],
                'sidebar' => $result['sidebar'],
                'style' => $result['style'],
                'url' => $result['url']
            ])
        );
    }

    $sql = "DROP TABLE IF EXISTS "._DB_PREFIX_."getresponse_webform";
    DB::getInstance()->execute($sql);
}
