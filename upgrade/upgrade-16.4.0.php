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

use GetResponse\Account\AccountSettingsRepository;
use GetResponse\CustomFields\CustomFieldsServiceFactory;
use GetResponse\Ecommerce\Ecommerce;
use GetResponse\Ecommerce\EcommerceRepository;
use GetResponse\Settings\Registration\RegistrationServiceFactory;
use GetResponse\Settings\Registration\RegistrationSettings;
use GetResponse\WebForm\WebForm;
use GetResponse\WebForm\WebFormRepository;
use GetResponse\WebTracking\WebTracking;
use GetResponse\WebTracking\WebTrackingRepository;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_16_4_0($object)
{
    $idShop = Context::getContext()->shop->id;
    upgradeCustomsTable($idShop);
    upgradeEcommerceTable($idShop);
    upgradeSettingsTable($idShop);
    upgradeWebFormsTable($idShop);

    return true;
}

function upgradeCustomsTable($idShop)
{
    $customFieldsService = CustomFieldsServiceFactory::create();
    $customFieldsService->setDefaultCustomFieldsMapping();

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
        $repository = new EcommerceRepository();
        $repository->updateEcommerceSubscription(
            new Ecommerce(
                Ecommerce::STATUS_ACTIVE,
                isset($result['gr_id_shop']) ? $result['gr_id_shop'] : null,
                isset($settings['campaign_id']) ? $settings['campaign_id'] : null
            )
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
        $accountRepository = new AccountSettingsRepository();
        $accountRepository->updateApiSettings($result['api_key'], $result['account_type'], $result['crypto']);

        $service = RegistrationServiceFactory::createService();
        $service->updateSettings(RegistrationSettings::createFromOldDbTable($result));

        $status = $result['active_tracking'] === 'yes' ? WebTracking::TRACKING_ACTIVE : WebTracking::TRACKING_INACTIVE;
        $webTrackingRepository = new WebTrackingRepository();
        $webTrackingRepository->updateWebTracking(
            new WebTracking(
                $status,
                $result['tracking_snippet']
            )
        );

        if (isset($result['invalid_request_date'])) {
            (new \GetResponse\Account\AccountRepository())->updateInvalidRequestDate($result['invalid_request_date']);
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
        $repository = new WebFormRepository();
        $repository->update(new WebForm(
            $result['active_subscription'] === 'yes' ? WebForm::STATUS_ACTIVE : WebForm::STATUS_INACTIVE,
            $result['webform_id'],
            $result['sidebar'],
            $result['style'],
            $result['url']
        ));
    }

    $sql = "DROP TABLE IF EXISTS "._DB_PREFIX_."getresponse_webform";
    DB::getInstance()->execute($sql);
}
