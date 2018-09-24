<?php

use GetResponse\CustomFieldsMapping\CustomFieldMapping;
use GrShareCode\DbRepositoryInterface;
use GrShareCode\ProductMapping\ProductMapping;
use GrShareCode\Job\Job as GrJob;
use GrShareCode\Job\JobCollection as GrJobCollection;

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
     * @param string $activeSubscription
     * @param string $campaignId
     * @param string $updateAddress
     * @param string $cycleDay
     * @param string $newsletter
     */
    public function updateSettings($activeSubscription, $campaignId, $updateAddress, $cycleDay, $newsletter)
    {
        $query = '
        UPDATE 
            ' .  _DB_PREFIX_ . 'getresponse_settings 
        SET
            `active_subscription` = "' . pSQL($activeSubscription) . '",
            `active_newsletter_subscription` = "' . pSQL($newsletter) . '",
            `campaign_id` = "' . pSQL($campaignId) . '",
            `update_address` = "' . pSQL($updateAddress) . '",
            `cycle_day` = "' . pSQL($cycleDay) . '"
        WHERE
            `id_shop` = ' . (int) $this->idShop;

        $this->db->execute($query);
    }

    /**
     * @param bool $isActive
     * @return array
     */
    public function getAutomationSettings($isActive = false)
    {
        $sql = '
        SELECT
            `id`, `id_shop`, `category_id`, `campaign_id`, `action`, `cycle_day`, `active`
        FROM
            ' .  _DB_PREFIX_ . 'getresponse_automation
        WHERE
            id_shop = ' . (int) $this->idShop;

        if ($isActive) {
            $sql .= ' AND `active` = "yes"';
        }

        if ($results = $this->db->ExecuteS($sql)) {
            return $results;
        }

        return array();
    }

    public function getGrShopId()
    {
        $sql = 'SELECT 
                    `gr_id_shop` 
                FROM 
                    `' . _DB_PREFIX_ . 'getresponse_ecommerce`
                WHERE
                    `id_shop` = ' . $this->idShop . ' 
                LIMIT 1';

        return $this->db->getValue($sql);
    }

    public function getApiKey()
    {
        $sql = 'SELECT 
                    `api_key` 
                FROM 
                    `' . _DB_PREFIX_ . 'getresponse_settings`
                WHERE
                    `id_shop` = ' . $this->idShop;

        return $this->db->getValue($sql);
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

        $mapping = $this->db->ExecuteS($sql);

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

        return $this->db->getValue($sql);
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

        return $this->db->getValue($sql);
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

        $mapping = $this->db->ExecuteS($sql);

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
     * @param bool $newsletterGuests
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getContacts($newsletterGuests = false)
    {
        if (version_compare(_PS_VERSION_, '1.7') === -1) {
            $newsletterTableName = _DB_PREFIX_ . 'newsletter';
            $newsletterModule = 'blocknewsletter';
        } else {
            $newsletterTableName = _DB_PREFIX_ . 'emailsubscription';
            $newsletterModule = _DB_PREFIX_ . 'emailsubscription';
        }
        $ngWhere = '';

        if ($newsletterGuests && $this->checkModuleStatus($newsletterModule)) {
            $ngWhere = 'UNION SELECT
                    0 as id,
                    "Friend" as firstname,
                    "" as lastname,
                    n.email as email,
                    "" as company,
                    "" as birthday,
                    "" as address1,
                    "" as address2,
                    "" as postcode,
                    "" as city,
                    "" as phone,
                    "" as country
                FROM
                    ' . $newsletterTableName . ' n
                WHERE
                    n.active = 1
                AND
                    id_shop = ' . (int) $this->idShop . '
            ';
        }

        $sql = 'SELECT
                    cu.id_customer as id,
                    cu.firstname as firstname,
                    cu.lastname as lastname,
                    cu.email as email,
                    cu.company as company,
                    cu.birthday as birthday,
                    ad.address1 as address1,
                    ad.address2 as address2,
                    ad.postcode as postcode,
                    ad.city as city,
                    ad.phone as phone,
                    co.iso_code as country
                FROM
                    ' . _DB_PREFIX_ . 'customer as cu
                LEFT JOIN
                    ' . _DB_PREFIX_ . 'address ad ON cu.id_customer = ad.id_customer
                LEFT JOIN
                    ' . _DB_PREFIX_ . 'country co ON ad.id_country = co.id_country
                WHERE
                    cu.newsletter = 1
                AND
                    cu.id_shop = ' . (int) $this->idShop . '
                    GROUP BY cu.email
                ' . $ngWhere;

        $contacts = $this->db->ExecuteS($sql);

        if (empty($contacts)) {
            return array();
        }

        foreach ($contacts as $id => $contact) {
            $contacts[$id]['category'] = $this->getContactCategory($contact['email']);
        }
        return $contacts;
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

        if ($results = $this->db->ExecuteS($sql)) {
            return $results[0];
        }

        return array();
    }

    /**
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getCustoms()
    {
        $sql = '
        SELECT
            `id_custom`,
            `id_shop`,
            `custom_field`,
            `custom_value`,
            `custom_name`,
            `default`,
            `active_custom`
        FROM
            ' . _DB_PREFIX_ . 'getresponse_customs
        WHERE
            id_shop = ' . (int) $this->idShop;

        if ($results = $this->db->ExecuteS($sql)) {
            return $results;
        }

        return array();
    }

    /**
     * @param string $moduleName
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function checkModuleStatus($moduleName)
    {
        if (empty($moduleName)) {
            return false;
        }

        $sql = '
        SELECT
            `active`
        FROM
            ' . _DB_PREFIX_ . 'module
        WHERE
            `name` = "' . pSQL($moduleName) . '"';

        if ($results = $this->db->ExecuteS($sql)) {
            if (isset($results[0]['active']) && 1 === (int) $results[0]['active']) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $email
     * @return string
     * @throws PrestaShopDatabaseException
     */
    private function getContactCategory($email)
    {
        $sql = '
        SELECT
            group_concat(DISTINCT cp.`id_category` separator ", ") as category
        FROM
            ' . _DB_PREFIX_ . 'customer as cu
        LEFT JOIN
            ' . _DB_PREFIX_ . 'address ad ON cu.`id_customer` = ad.`id_customer`
        LEFT JOIN
            ' . _DB_PREFIX_ . 'country co ON ad.`id_country` = co.`id_country`
        LEFT JOIN
            ' . _DB_PREFIX_ . 'orders o ON o.`id_customer` = cu.`id_customer`
        LEFT JOIN
            ' . _DB_PREFIX_ . 'order_detail od ON (od.`id_order` = o.`id_order` 
            AND o.`id_shop` = ' . (int) $this->idShop . ')
        LEFT JOIN
            ' . _DB_PREFIX_ . 'category_product cp ON (cp.`id_product` = od.`product_id` 
            AND od.`id_shop` = ' . (int) $this->idShop . ')
        LEFT JOIN
            ' . _DB_PREFIX_ . 'category_lang cl ON (cl.`id_category` = cp.`id_category` 
            AND cl.`id_shop` = ' .
            (int) $this->idShop . ' AND cl.`id_lang` = cu.`id_lang`)
        WHERE
            cu.`newsletter` = 1
            AND cu.`email` = "' . pSQL($email) . '"
            AND cu.`id_shop` = ' . (int) $this->idShop;

        $categories = $this->db->ExecuteS($sql);

        if (empty($categories)) {
            return '';
        }
        return $categories[0]['category'];
    }

    /**
     * @param $customerId
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public function getOrders($customerId)
    {
        $sql = '
        SELECT
            `id_order`,
            `id_cart`
        FROM
            ' . _DB_PREFIX_ . 'orders
        WHERE
            `id_shop` = ' . (int) $this->idShop . ' AND
            `id_customer` = ' . (int) $customerId;

        if ($results = $this->db->ExecuteS($sql)) {
            return $results;
        }

        return array();
    }

    /**
     * @param GrJob $job
     */
    public function addJob(GrJob $job)
    {
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'getresponse_jobs
                SET
                    `name` = "' . $this->db->escape($job->getName()) . '",
                    `content` = "' . $this->db->escape($job->getMessageContent()) .'"';
        $this->db->execute($sql);
    }

    /**
     * @return GrJobCollection
     */
    public function getJobsToProcess()
    {
        $collection = new GrJobCollection();

        $sql = '
        SELECT
            `name`,
            `content`
        FROM
            ' . _DB_PREFIX_ . 'getresponse_jobs'
        ;

        if ($results = $this->db->ExecuteS($sql)) {
            foreach ($results as $result) {
                $collection->add(new GrJob($result['name'], $result['content']));
            }
        }

        return $collection;
    }

    /**
     * @param GrJob $job
     */
    public function deleteJob(GrJob $job)
    {
        $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'getresponse_jobs
                WHERE
                    `name` = "' . $this->db->escape($job->getName()) . '" AND
                    `content` = "' . $this->db->escape($job->getMessageContent()) . '"';
        $this->db->execute($sql);
    }

    /**
     * @param string $apiKey
     * @param string $accountType
     * @param string $crypto
     */
    public function updateApiSettings($apiKey, $accountType, $crypto)
    {
        $query = '
        UPDATE 
            ' .  _DB_PREFIX_ . 'getresponse_settings 
        SET
            `api_key` = "' . pSQL($apiKey) . '",
            `account_type` = "' . pSQL($accountType) . '",
            `crypto` = "' . pSQL($crypto) . '"
         WHERE
            `id_shop` = ' . (int) $this->idShop;

        $this->db->execute($query);
    }

    /**
     * @param $activeTracking
     * @param $snippet
     */
    public function updateTracking($activeTracking, $snippet)
    {
        $query = '
        UPDATE 
            ' . _DB_PREFIX_ . 'getresponse_settings
        SET
            `active_tracking` = "' . pSQL($activeTracking) . '",
            `tracking_snippet` = "' . pSQL($snippet, true) . '"
        WHERE
            `id_shop` = ' . (int) $this->idShop;

        $this->db->execute($query);
    }

    /**
     * @return array
     */
    public function getWebformSettings()
    {
        $query = '
        SELECT
            `webform_id`, 
            `active_subscription`, 
            `sidebar`, 
            `style`, 
            `url`
        FROM
            ' . _DB_PREFIX_ . 'getresponse_webform
        WHERE
            `id_shop` = ' . (int) $this->idShop;

        if ($results = $this->db->ExecuteS($query)) {
            return $results[0];
        }

        return array();
    }

    /**
     * @param string $activeSubscription
     */
    public function updateWebformSubscription($activeSubscription)
    {
        $query = '
        UPDATE 
            ' . _DB_PREFIX_ . 'getresponse_webform 
        SET
            `active_subscription` = "' . pSQL($activeSubscription) . '"
        WHERE
            `id_shop` = ' . (int) $this->idShop;

        $this->db->execute($query);
    }

    /**
     * @param int $webformId
     * @param string $activeSubscription
     * @param string $sidebar
     * @param string $style
     * @param string $url
     */
    public function updateWebformSettings($webformId, $activeSubscription, $sidebar, $style, $url)
    {
        $query = '
        UPDATE 
            ' . _DB_PREFIX_ . 'getresponse_webform
        SET
            `webform_id` = "' . pSQL($webformId) . '",
            `active_subscription` = "' . pSQL($activeSubscription) . '",
            `sidebar` = "' . pSQL($sidebar) . '",
            `style` = "' . pSQL($style) . '",
            `url` = "' . pSQL($url) . '"
        WHERE
            `id_shop` = ' . (int) $this->idShop;

        $this->db->execute($query);
    }

    /**
     * @param CustomFieldMapping $custom
     */
    public function updateCustom(CustomFieldMapping $custom)
    {
        $sql = '
                UPDATE
                    ' . _DB_PREFIX_ . 'getresponse_customs
                SET
                    `custom_name` = "' . pSQL($custom->getName()) . '",
                    `active_custom` = "' . pSQL($custom->getActive()) . '"
                WHERE
                    `id_shop` = ' . (int) $this->idShop . '
                    AND `id_custom` = "' . pSQL($custom->getId()) . '"';

        $this->db->Execute($sql);
    }

    public function clearDatabase()
    {
        $this->db->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'getresponse_settings`;');
        $this->db->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'getresponse_customs`;');
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
			`account_type` enum(\'smb\',\'360en\',\'360pl\') NOT NULL DEFAULT \'smb\',
			`crypto` char(32) NULL,
			PRIMARY KEY (`id`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'getresponse_customs` (
			`id_custom` int(11) NOT NULL AUTO_INCREMENT,
			`id_shop` int(6) NOT NULL,
			`custom_field` char(32) NOT NULL,
			`custom_value` char(32) NOT NULL,
			`custom_name` char(32) NOT NULL,
			`default` enum(\'yes\',\'no\') NOT NULL DEFAULT \'no\',
			`active_custom` enum(\'yes\',\'no\') NOT NULL DEFAULT \'no\',
			PRIMARY KEY (`id_custom`)
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

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'getresponse_automation` (
			`id` int(6) NOT NULL AUTO_INCREMENT,
			`id_shop` int(6) NOT NULL,
			`category_id` char(32) NOT NULL,
			`campaign_id` char(32) NOT NULL,
			`action` char(32) NOT NULL DEFAULT \'move\',
			`cycle_day` char(5) NOT NULL,
			`active` enum(\'yes\',\'no\') NOT NULL DEFAULT \'yes\',
			PRIMARY KEY (`id`),
			UNIQUE KEY `id_shop` (`id_shop`,`category_id`,`campaign_id`)
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

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ps_getresponse_jobs` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(32) DEFAULT NULL,
              `content` text,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ps_getresponse_carts` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `shop_id` int(11) DEFAULT NULL,
              `gr_shop_id` varchar(16) DEFAULT NULL,
              `cart_id` int(11) DEFAULT NULL,
              `gr_cart_id` varchar(16) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE `ps_getresponse_orders` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `shop_id` int(11) DEFAULT NULL,
              `gr_shop_id` varchar(16) DEFAULT NULL,
              `order_id` int(11) DEFAULT NULL,
              `gr_order_id` varchar(16) DEFAULT NULL,
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
                    $sql[] = $this->sqlCustomsSetting($shop['id_shop']);
                }
            }
        } else {
            $sql[] = $this->sqlMainSetting('1');
            $sql[] = $this->sqlWebformSetting('1');
            $sql[] = $this->sqlCustomsSetting('1');
        }

        //Install SQL
        foreach ($sql as $s) {
            try {
                Db::getInstance()->Execute($s);
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
            ' . (int) $storeId . ', \'\', \'no\', \'no\', \'no\', \'\', \'no\', \'0\', \' \', \'gr\', \'\'
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
     *
     * @return string
     */
    private function sqlCustomsSetting($storeId)
    {
        return '
        INSERT INTO `' . _DB_PREFIX_ . 'getresponse_customs` (
            `id_shop` ,
            `custom_field`,
            `custom_value`,
            `custom_name`,
            `default`,
            `active_custom`
        )
        VALUES
            (' . (int) $storeId . ', \'firstname\', \'firstname\', \'\', \'no\', \'no\'),
            (' . (int) $storeId . ', \'lastname\', \'lastname\', \'\', \'no\', \'no\'),
            (' . (int) $storeId . ', \'email\', \'email\', \'\', \'yes\', \'no\'),
            (' . (int) $storeId . ', \'address\', \'address1\', \'\', \'no\', \'no\'),
            (' . (int) $storeId . ', \'postal\', \'postcode\', \'\', \'no\', \'no\'),
            (' . (int) $storeId . ', \'city\', \'city\', \'\', \'no\', \'no\'),
            (' . (int) $storeId . ', \'phone\', \'phone\', \'\', \'no\', \'no\'),
            (' . (int) $storeId . ', \'country\', \'country\', \'\', \'no\', \'no\'),
            (' . (int) $storeId . ', \'birthday\', \'birthday\', \'\', \'no\', \'no\'),
            (' . (int) $storeId . ', \'company\', \'company\', \'\', \'no\', \'no\'),
            (' . (int) $storeId . ', \'category\', \'category\', \'\', \'no\', \'no\');';
    }

    /**
     * @param string $grShopId
     * @param int $externalCartId
     * @param string $grCartId
     */
    public function removeCartMapping($grShopId, $externalCartId, $grCartId)
    {
        // TODO: Implement removeCartMapping() method.
    }

    /**
     * @param string $grShopId
     * @param int $externalOrderId
     * @return string
     */
    public function getPayloadMd5FromOrderMapping($grShopId, $externalOrderId)
    {
        $query = '
        SELECT
            `payload_md5`
        FROM
            ' . _DB_PREFIX_ . 'getresponse_orders
        WHERE
            `shop_id` = ' . (int) $this->idShop . '
            AND `order_id` = ' . (int) $this->idShop;

        if ($results = $this->db->ExecuteS($query)) {
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
            `shop_id` = ' . (int) $this->idShop;

        if ($results = $this->db->ExecuteS($query)) {
            return $results[0];
        }

        return '';
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
}
