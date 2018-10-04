<?php
namespace GetResponse\Account;

use Db;
use GetResponse\CustomFieldsMapping\CustomFieldMapping;
use PrestaShopDatabaseException;

/**
 * Class AccountSettingsRepository
 * @package GetResponse\Account
 */
class AccountSettingsRepository
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
     * @return null|AccountSettings
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
            `id_shop` = ' . (int)$this->idShop;

        if ($results = $this->db->ExecuteS($sql)) {
            return AccountSettingsFactory::fromDb($results[0]);
        }

        return null;
    }

    /**
     * @param string $trackingStatus
     * @param string $snippet
     */
    public function updateTracking($trackingStatus, $snippet)
    {
        $query = '
        UPDATE 
            ' . _DB_PREFIX_ . 'getresponse_settings
        SET
            `active_tracking` = "' . pSQL($trackingStatus) . '",
            `tracking_snippet` = "' . pSQL($snippet, true) . '"
        WHERE
            `id_shop` = ' . (int)$this->idShop;

        $this->db->execute($query);
    }

    /**
     * @param string $apiKey
     * @param string $accountType
     * @param string $domain
     */
    public function updateApiSettings($apiKey, $accountType, $domain)
    {
        $query = '
        UPDATE 
            ' .  _DB_PREFIX_ . 'getresponse_settings 
        SET
            `api_key` = "' . pSQL($apiKey) . '",
            `account_type` = "' . pSQL($accountType) . '",
            `crypto` = "' . pSQL($domain) . '"
         WHERE
            `id_shop` = ' . (int) $this->idShop;

        $this->db->execute($query);
    }

    public function disconnectApiSettings()
    {
        $query = '
        UPDATE 
            ' .  _DB_PREFIX_ . 'getresponse_settings 
        SET
            `api_key` = null,
            `active_subscription` = "no",
            `active_newsletter_subscription` = "no",
            `active_tracking` = "no",
            `tracking_snippet` = "",
            `update_address` = "no",
            `campaign_id` = "",
            `cycle_day` = "",
            `cycle_day` = "",
            `account_type` = "' . pSQL(AccountSettings::ACCOUNT_TYPE_SMB) . '",
            `crypto` = null
         WHERE
            `id_shop` = ' . (int) $this->idShop;
        $this->db->execute($query);

        $query = '
        UPDATE 
            ' .  _DB_PREFIX_ . 'getresponse_webform 
        SET
            `webform_id` = "",
            `active_subscription` = "no",
            `sidebar` = "left",
            `style` = "webform" 
         WHERE
            `id_shop` = ' . (int) $this->idShop;
        $this->db->execute($query);

        $query = '
        DELETE FROM
            ' . _DB_PREFIX_ . 'getresponse_ecommerce 
        WHERE
            `id_shop` = ' . (int) $this->idShop;
        $this->db->execute($query);

        $sql = '
                UPDATE
                    ' . _DB_PREFIX_ . 'getresponse_customs
                SET
                    `custom_name` = "",
                    `active_custom` = "' . pSQL(CustomFieldMapping::INACTIVE) . '"
                WHERE
                    `id_shop` = ' . (int) $this->idShop . '
                    AND `default` = "' . pSQL(CustomFieldMapping::DEFAULT_NO) . '"';
        $this->db->execute($sql);
    }

}