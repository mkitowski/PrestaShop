<?php
namespace GetResponse\Settings;

use Db;
use GrShareCode\TrackingCode\TrackingCode;

/**
 * Class Repository
 * @package GetResponse\Settings
 */
class SettingsRepository
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
     * @return null|Settings
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
            return SettingsFactory::fromDb($results[0]);
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

}