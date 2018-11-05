<?php


if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_16_3_4($object) {

    $sql = "ALTER TABLE `"._DB_PREFIX_."getresponse_settings` ADD `origin_custom_id` VARCHAR(16)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT ''  AFTER `crypto`";
    Db::getInstance()->Execute($sql);

    return true;
}