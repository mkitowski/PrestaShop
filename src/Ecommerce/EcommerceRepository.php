<?php
namespace GetResponse\Ecommerce;

use Db;

/**
 * Class EcommerceRepository
 */
class EcommerceRepository
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
     * @return Ecommerce|null
     */
    public function getEcommerceSettings()
    {
        $query = 'SELECT * FROM
                    ' . _DB_PREFIX_ . 'getresponse_ecommerce 
                WHERE
                    `id_shop` = ' . (int)$this->idShop;

        if ($results = $this->db->ExecuteS($query)) {
            return Ecommerce::fromDb($results[0]);
        }

        return null;
    }

    /**
     * @param bool $isEnabled
     */
    public function updateEcommerceSubscription($isEnabled)
    {
        if ($isEnabled) {
            $query = '
                INSERT INTO 
                    ' . _DB_PREFIX_ . 'getresponse_ecommerce 
                SET
                    `id_shop` = ' . (int) $this->idShop . '
            ';
        } else {
            $query = '
                DELETE FROM
                    ' . _DB_PREFIX_ . 'getresponse_ecommerce 
                WHERE
                    `id_shop` = ' . (int) $this->idShop;
        }

        $this->db->execute($query);
    }

    /**
     * @param string $shopId
     */
    public function updateEcommerceShopId($shopId)
    {
        $query = '
            UPDATE
                ' . _DB_PREFIX_ . 'getresponse_ecommerce 
            SET
                `gr_id_shop` = "' . $shopId . '"
            WHERE
                `id_shop` = ' . (int) $this->idShop;

        $this->db->execute($query);
    }

}