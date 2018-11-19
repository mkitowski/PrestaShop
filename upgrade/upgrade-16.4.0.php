<?php


use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Ecommerce\EcommerceDto;
use GetResponse\Ecommerce\EcommerceRepository;
use GetResponse\Settings\Registration\RegistrationRepository;
use GetResponse\Settings\Registration\RegistrationSettings;
use GetResponse\WebForm\WebForm;
use GetResponse\WebForm\WebFormRepository;
use GetResponse\WebTracking\WebTracking;
use GetResponse\WebTracking\WebTrackingRepository;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_16_4_0($object) {

    upgradeCustomsTable();
    upgradeEcommerceTable();
    upgradeSettingsTable();
    upgradeWebFormsTable();

    return true;
}

function upgradeCustomsTable() {
    $sql = "SELECT * FROM "._DB_PREFIX_."getresponse_customs";
    $result = Db::getInstance()->executeS($sql);

    Configuration::updateValue(ConfigurationSettings::CUSTOM_FIELDS, json_encode($result));

    $sql = "DROP TABLE "._DB_PREFIX_."getresponse_customs";
    DB::getInstance()->execute($sql);
}

function upgradeEcommerceTable() {
    $sql = "SELECT * FROM "._DB_PREFIX_."getresponse_ecommerce";
    $result = Db::getInstance()->executeS($sql);

    if (!empty($result)) {
        $repository = new EcommerceRepository();
        $repository->updateEcommerceSubscription(new EcommerceDto($result['gr_id_shop'], EcommerceDto::STATUS_ACTIVE));
    }

    $sql = "DROP TABLE "._DB_PREFIX_."getresponse_ecommerce";
    DB::getInstance()->execute($sql);
}

function upgradeSettingsTable() {
    $sql = "SELECT * FROM "._DB_PREFIX_."getresponse_settings";
    $result = Db::getInstance()->executeS($sql);

    if (!empty($result)) {
        $accountRepository = new AccountSettingsRepository();
        $accountRepository->updateApiSettings($result['api_key'], $result['account_type'], $result['crypto']);

        $registrationRepository = new RegistrationRepository();
        $registrationRepository->updateSettings(RegistrationSettings::createFromOldDbTable($result));

        $webTrackingRepository = new WebTrackingRepository();
        $webTrackingRepository->saveTracking(new WebTracking($result['active_tracking'], $result['tracking_snippet']));

        if (isset($result['invalid_request_date'])) {
            Configuration::updateValue(ConfigurationSettings::INVALID_REQUEST, $result['invalid_request_date']);
        }

        if (isset($result['origin_custom_id'])) {
            Configuration::updateValue(ConfigurationSettings::ORIGIN_CUSTOM_FIELD, $result['origin_custom_id']);
        }
    }

    $sql = "DROP TABLE "._DB_PREFIX_."getresponse_settings";
    DB::getInstance()->execute($sql);
}

function upgradeWebFormsTable() {
    $sql = "SELECT * FROM "._DB_PREFIX_."getresponse_webform";
    $result = Db::getInstance()->executeS($sql);

    if (!empty($result)) {
        $repository = new WebFormRepository();
        $repository->update(new WebForm(
            $result['webform_id'],
            $result['active_subsciption'],
            $result['sidebar'],
            $result['style'],
            $result['url']
        ));
    }

    $sql = "DROP TABLE "._DB_PREFIX_."getresponse_webform";
    DB::getInstance()->execute($sql);
}

