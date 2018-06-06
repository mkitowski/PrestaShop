<?php

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
     */
    public function saveOrderMapping($grShopId, $externalOrderId, $grOrderId)
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

}
