<?php
namespace GetResponse\WebTracking;

use Db;
use PrestaShopDatabaseException;

/**
 * Class WebTrackingRepository
 * @package GetResponse\WebTracking
 */
class WebTrackingRepository
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
     * @return WebTracking|null
     * @throws PrestaShopDatabaseException
     */
    public function getWebTracking()
    {
        $sql = '
        SELECT 
            `active_tracking`,
            `tracking_snippet` 
        FROM
            ' . _DB_PREFIX_ . 'getresponse_settings
        WHERE
            `id_shop` = ' . (int)$this->idShop;

        if ($results = $this->db->ExecuteS($sql)) {
            return new WebTracking(
                $results[0]['active_tracking'],
                $results[0]['tracking_snippet']
            );
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

}