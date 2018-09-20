<?php
namespace GetResponse\WebForm;

use Db;
use PrestaShopDatabaseException;

/**
 * Class WebFormRepository
 */
class WebFormRepository
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
     * @param WebForm $webForm
     */
    public function update(WebForm $webForm)
    {
        $query = '
        UPDATE 
            ' . _DB_PREFIX_ . 'getresponse_webform
        SET
            `webform_id` = "' . pSQL($webForm->getId()) . '",
            `active_subscription` = "' . pSQL($webForm->getStatus()) . '",
            `sidebar` = "' . pSQL($webForm->getSidebar()) . '",
            `style` = "' . pSQL($webForm->getStyle()) . '",
            `url` = "' . pSQL($webForm->getUrl()) . '"
        WHERE
            `id_shop` = ' . (int)$this->idShop;

        $this->db->execute($query);
    }

    /**
     * @return WebForm|null
     * @throws PrestaShopDatabaseException
     */
    public function getWebForm()
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
            return WebFormFactory::fromDb($results[0]);
        }

        return null;
    }

    /**
     * @param string $activeSubscription
     */
    public function updateWebFormSubscription($activeSubscription)
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
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getWebformSettings()
    {
        $sql = '
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

        if ($results = $this->db->ExecuteS($sql)) {
            return $results[0];
        }

        return [];
    }


}