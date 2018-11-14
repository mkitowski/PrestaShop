<?php

use GetResponse\CustomFields\DefaultCustomFields;
use GetResponse\CustomFieldsMapping\CustomFieldMapping;
use GrShareCode\DbRepositoryInterface;
use GrShareCode\ProductMapping\ProductMapping;

class GetResponseRepository implements DbRepositoryInterface
{
    /** @var Db */
    private $db;

    /** @var int */
    private $idShop;

    /**
     * @param Db $db
     * @param int $shopId
     */
    public function __construct($db, $shopId)
    {
        $this->db = $db;
        $this->idShop = $shopId;
    }

    /**
     * @param string $grShopId
     * @param int $externalProductId
     * @param int $externalVariantId
     * @return ProductMapping
     * @throws PrestaShopDatabaseException
     */
    public function getProductMappingByVariantId(
        $grShopId,
        $externalProductId,
        $externalVariantId
    ) {
        $sql = 'SELECT
                    `product_id`,
                     `gr_product_id`,
                     `gr_shop_id`,
                     `gr_variant_id`,
                     `variant_id`
               FROM
                    ' . _DB_PREFIX_ . 'getresponse_products
               WHERE
                    `gr_shop_id` = "' . $grShopId . '" AND
                    `product_id` = ' . $externalProductId . ' AND
                    `variant_id` = ' . $externalVariantId . '
               ';

        $mapping = $this->db->executeS($sql, true, false);

        if (empty($mapping)) {
            return new ProductMapping(null, null, null, null, null);
        }

        return new ProductMapping(
            $mapping[0]['product_id'],
            $mapping[0]['variant_id'],
            $mapping[0]['gr_shop_id'],
            $mapping[0]['gr_product_id'],
            $mapping[0]['gr_variant_id']
        );
    }

    /**
     * @param string $grShopId
     * @param int $externalCartId
     * @param string $grCartId
     */
    public function saveCartMapping($grShopId, $externalCartId, $grCartId)
    {
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'getresponse_carts 
                SET
                    `gr_shop_id` = "' . $this->db->escape($grShopId) . '",
                    `gr_cart_id` = "' . $grCartId .'",
                    `cart_id` = ' . (int) $externalCartId;
        $this->db->execute($sql);
    }

    /**
     * @param string $grShopId
     * @param int $externalCartId
     * @return null|string
     */
    public function getGrCartIdFromMapping($grShopId, $externalCartId)
    {
        $sql = 'SELECT `gr_cart_id` FROM
                    ' . _DB_PREFIX_ . 'getresponse_carts 
                WHERE
                    `gr_shop_id` = "' . $this->db->escape($grShopId) . '" AND 
                    `cart_id` = ' . (int) $externalCartId;

        return $this->db->getValue($sql, false);
    }

    /**
     * @param string $grShopId
     * @param int $externalOrderId
     * @return null|string
     */
    public function getGrOrderIdFromMapping($grShopId, $externalOrderId)
    {
        $sql = 'SELECT `gr_order_id` FROM
                    ' . _DB_PREFIX_ . 'getresponse_orders
                WHERE
                    `gr_shop_id` = "' . $this->db->escape($grShopId) . '" AND 
                    `order_id` = ' . (int) $externalOrderId;

        return $this->db->getValue($sql, false);
    }

    /**
     * @param string $grShopId
     * @param int $externalOrderId
     * @param string $grOrderId
     * @param string $payloadMd5
     */
    public function saveOrderMapping($grShopId, $externalOrderId, $grOrderId, $payloadMd5)
    {
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'getresponse_orders
                SET
                    `gr_shop_id` = "' . $this->db->escape($grShopId) . '",
                    `gr_order_id` = "' . $this->db->escape($grOrderId) .'",
                    `payload_md5` = "' . $this->db->escape($payloadMd5) .'",
                    `order_id` = ' . (int) $externalOrderId;
        $this->db->execute($sql);
    }

    /**
     * @param string $grShopId
     * @param int $externalProductId
     * @return ProductMapping|null
     * @throws PrestaShopDatabaseException
     */
    public function getProductMappingByProductId($grShopId, $externalProductId)
    {
        $sql = 'SELECT
                    `product_id`,
                     `gr_product_id`,
                     `gr_shop_id`,
                     `gr_variant_id`,
                     `variant_id`
               FROM
                    ' . _DB_PREFIX_ . 'getresponse_products
               WHERE
                    `gr_shop_id` = "' . $this->db->escape($grShopId) . '" AND
                    `product_id` = "' . $this->db->escape($externalProductId) . '"
               ';

        $mapping = $this->db->executeS($sql, true, false);

        if (empty($mapping)) {
            return new ProductMapping(null, null, null, null, null);
        }

        return new ProductMapping(
            $mapping[0]['product_id'],
            $mapping[0]['variant_id'],
            $mapping[0]['gr_shop_id'],
            $mapping[0]['gr_product_id'],
            $mapping[0]['gr_variant_id']
        );
    }

    /**
     * @param ProductMapping $productMapping
     */
    public function saveProductMapping(ProductMapping $productMapping)
    {
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'getresponse_products
                SET
                    `product_id` = "' . $productMapping->getExternalProductId() . '",
                    `gr_product_id` = "' . $productMapping->getGrProductId() . '",
                    `gr_shop_id` = "' . $productMapping->getGrShopId() . '",
                    `gr_variant_id` = "' . $productMapping->getGrVariantId() . '",
                    `variant_id` = "' . $productMapping->getExternalVariantId() . '"';
        $this->db->execute($sql);
    }


    /**
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getSettings()
    {
        $sql = '
        SELECT
            `id`,
            `id_shop`,
            `api_key`,
            `active_subscription`,
            `active_newsletter_subscription`,
            `active_tracking`,
            `tracking_snippet`,
            `update_address`,
            `campaign_id`,
            `cycle_day`,
            `account_type`,
            `crypto`
        FROM
            ' . _DB_PREFIX_ . 'getresponse_settings
        WHERE
            `id_shop` = ' . (int) $this->idShop;

        if ($results = $this->db->executeS($sql, true, false)) {
            return $results[0];
        }

        return array();
    }

    /**
     * @return array
     */
    public function getCustoms()
    {
        return json_decode(ConfigurationSettings::get(ConfigurationSettings::GETRESPONSE_CUSTOMS), true);
    }

    /**
     * @param CustomFieldMapping $custom
     */
    public function updateCustom(CustomFieldMapping $custom)
    {
        $customs = $this->getCustoms();

        foreach ($customs as $_custom) {
            if ($_custom['id_custom'] === $custom->getId()) {
                $_custom['custom_name'] = $custom->getName();
                $_custom['active_custom'] = $custom->getActive();
            }
        }

        ConfigurationSettings::updateValue(ConfigurationSettings::GETRESPONSE_CUSTOMS, json_encode($customs));
    }

    public function clearDatabase()
    {
        $this->db->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'getresponse_settings`;');
        ConfigurationSettings::updateValue(ConfigurationSettings::GETRESPONSE_CUSTOMS, NULL);
        $this->db->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'getresponse_webform`;');
        $this->db->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'getresponse_automation`;');
        $this->db->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'getresponse_ecommerce`;');
        $this->db->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'getresponse_products`;');
        $this->db->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'getresponse_subscribers`;');
        $this->db->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'getresponse_jobs`;');
        $this->db->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'getresponse_carts`;');
        $this->db->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'getresponse_orders`;');
    }

    public function prepareDatabase()
    {
        $sql = array();

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'getresponse_settings` (
			`id` int(6) NOT NULL AUTO_INCREMENT,
			`id_shop` char(32) NOT NULL,
			`api_key` char(32) NOT NULL,
			`active_subscription` enum(\'yes\',\'no\') NOT NULL DEFAULT \'no\',
			`active_newsletter_subscription` enum(\'yes\',\'no\') NOT NULL DEFAULT \'no\',
			`active_tracking` enum(\'yes\',\'no\', \'disabled\') NOT NULL DEFAULT \'disabled\',
			`tracking_snippet` text,
			`update_address` enum(\'yes\',\'no\') NOT NULL DEFAULT \'no\',
			`campaign_id` char(5) NOT NULL,
			`cycle_day` char(5) NOT NULL,
            `account_type` enum(\'smb\',\'mx_us\',\'mx_pl\') NOT NULL DEFAULT \'smb\',
			`crypto` char(32) NULL,
			`invalid_request_date` DATETIME NULL,
			PRIMARY KEY (`id`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'getresponse_webform` (
			`id` int(6) NOT NULL AUTO_INCREMENT,
			`id_shop` int(6) NOT NULL,
			`webform_id` char(32) NOT NULL,
			`active_subscription` enum(\'yes\',\'no\') NOT NULL DEFAULT \'no\',
			`sidebar` enum(\'left\',\'right\',\'header\',\'top\',\'footer\',\'home\') NOT NULL DEFAULT \'home\',
			`style` enum(\'webform\',\'prestashop\') NOT NULL DEFAULT \'webform\',
			`url` varchar(255) DEFAULT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'getresponse_ecommerce` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `id_shop` int(11) DEFAULT NULL,
            `gr_id_shop` varchar(16) DEFAULT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'getresponse_products` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `product_id` int(11) DEFAULT NULL,
              `gr_product_id` varchar(16) DEFAULT NULL,
              `gr_shop_id` varchar(16) DEFAULT NULL,
              `gr_variant_id` varchar(16) DEFAULT NULL,
              `variant_id` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'getresponse_subscribers` (
            `id_user` int(11) unsigned NOT NULL,
            `id_campaign` varchar(16) DEFAULT NULL,
            `gr_id_user` varchar(16) DEFAULT NULL,           
            `email` varchar(128) DEFAULT NULL,
            UNIQUE KEY `id_user` (`id_user`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

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

        $sql[] = 'CREATE TABLE `' . _DB_PREFIX_  .'getresponse_orders` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `gr_shop_id` varchar(16) DEFAULT NULL,
              `order_id` int(11) DEFAULT NULL,
              `gr_order_id` varchar(16) DEFAULT NULL,
              `payload_md5` text,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

        $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'cart` ADD `cart_hash` varchar(32);';
        $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'cart` ADD `gr_id_cart` varchar(32);';
        $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'orders` ADD `gr_id_order` varchar(32);';

        //multistore
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
            $shops = Shop::getShops();

            if (!empty($shops) && is_array($shops)) {
                foreach ($shops as $shop) {
                    if (empty($shop['id_shop'])) {
                        continue;
                    }
                    $sql[] = $this->sqlMainSetting($shop['id_shop']);
                    $sql[] = $this->sqlWebformSetting($shop['id_shop']);
                    $sql[] = $this->setDefaultCustomFields($shop['id_shop']);
                }
            }
        } else {
            $sql[] = $this->sqlMainSetting('1');
            $sql[] = $this->sqlWebformSetting('1');
            $sql[] = $this->setDefaultCustomFields('1');
        }

        //Install SQL
        foreach ($sql as $s) {
            try {
                Db::getInstance()->execute($s);
            } catch (Exception $e) {
            }
        }
    }


    /**
     * @param int $storeId
     *
     * @return string
     */
    private function sqlMainSetting($storeId)
    {
        return '
        INSERT INTO `' . _DB_PREFIX_ . 'getresponse_settings` (
            `id_shop` ,
            `api_key` ,
            `active_subscription` ,
            `active_newsletter_subscription` ,
            `active_tracking` ,
            `tracking_snippet`,
            `update_address` ,
            `campaign_id` ,
            `cycle_day` ,
            `account_type` ,
            `crypto`
        )
        VALUES (
            ' . (int) $storeId . ', \'\', \'no\', \'no\', \'no\', \'\', \'no\', \'0\', \' \', \'smb\', \'\'
        )
        ON DUPLICATE KEY UPDATE `id` = `id`;';
    }

    /**
     * @param int $storeId
     * @return string
     */
    private function sqlWebformSetting($storeId)
    {
        return '
        INSERT INTO  `' . _DB_PREFIX_ . 'getresponse_webform` (
            `id_shop` ,
            `webform_id` ,
            `active_subscription` ,
            `sidebar`,
            `style`
        )
        VALUES (
            ' . (int) $storeId . ',  \'\',  \'no\',  \'left\',  \'webform\'
        )
        ON DUPLICATE KEY UPDATE `id` = `id`;';
    }

    /**
     * @param int $storeId
     */
    private function setDefaultCustomFields($storeId)
    {
        $customFields = DefaultCustomFields::DEFAULT_CUSTOM_FIELDS;

        foreach ($customFields as $field) {
            $field['id_shop'] = $storeId;
        }

        ConfigurationSettings::updateValue(ConfigurationSettings::GETRESPONSE_CUSTOMS, json_encode($customFields));
    }

    /**
     * @param string $grShopId
     * @param int $externalCartId
     * @param string $grCartId
     */
    public function removeCartMapping($grShopId, $externalCartId, $grCartId)
    {
        $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'getresponse_carts
                WHERE
                    `gr_shop_id` = "' . $this->db->escape($grShopId) . '" AND 
                    `gr_cart_id` = "' . $this->db->escape($grCartId) . '" AND 
                    `cart_id` = ' . (int) $externalCartId;
        $this->db->execute($sql);
    }

    /**
     * @param string $grShopId
     * @param int $externalOrderId
     * @return string
     * @throws PrestaShopDatabaseException
     */
    public function getPayloadMd5FromOrderMapping($grShopId, $externalOrderId)
    {
        $query = '
        SELECT
            `payload_md5`
        FROM
            ' . _DB_PREFIX_ . 'getresponse_orders
        WHERE
            `gr_shop_id` = "' . $this->db->escape($grShopId) . '" AND 
            `order_id` = ' . (int) $externalOrderId;

        if ($results = $this->db->executeS($query)) {
            return $results[0];
        }

        return '';
    }

    /**
     * @param int $accountId
     */
    public function markAccountAsInvalid($accountId)
    {
        $query = '
        UPDATE 
            ' .  _DB_PREFIX_ . 'getresponse_settings 
        SET
            `invalid_request_date` = NOW()
   
        WHERE
            `id_shop` = ' . (int) $this->idShop;
        $this->db->execute($query);
    }

    /**
     * @param $accountId
     */
    public function markAccountAsValid($accountId)
    {
        $query = '
        UPDATE 
            ' .  _DB_PREFIX_ . 'getresponse_settings 
        SET
            `invalid_request_date` = NULL
   
        WHERE
            `id_shop` = ' . (int) $this->idShop;
        $this->db->execute($query);
    }

    /**
     * @param int $accountId
     * @return string
     */
    public function getInvalidAccountFirstOccurrenceDate($accountId)
    {
        $query = '
        SELECT
            `invalid_request_date`
        FROM
            ' . _DB_PREFIX_ . 'getresponse_settings
        WHERE
            `id_shop` = ' . (int) $this->idShop;

        return $this->db->getValue($query, false);
    }

    /**
     * @param int $accountId
     */
    public function disconnectAccount($accountId)
    {
        $query = '
        UPDATE 
            ' .  _DB_PREFIX_ . 'getresponse_settings 
        SET
            `api_key` = "",
            `account_type` = "",
            `crypto` = ""
         WHERE
            `id_shop` = ' . (int) $this->idShop;

        $this->db->execute($query);
    }

    /**
     * @return string
     */
    public function getOriginCustomFieldId()
    {
        $query = "SELECT `origin_custom_id` FROM " . _DB_PREFIX_ . "getresponse_settings WHERE `id_shop` = " . (int) $this->idShop;
        return $this->db->getValue($query, false);
    }

    /**
     * @param string $id
     */
    public function setOriginCustomFieldId($id)
    {
        $query = "UPDATE " .  _DB_PREFIX_ . "getresponse_settings SET origin_custom_id = '" . pSQL($id) . "' WHERE `id_shop` = " . (int) $this->idShop;
        $this->db->execute($query);
    }

    public function clearOriginCustomField()
    {
        $this->setOriginCustomFieldId('');
    }


}
