<?php
namespace GetResponse\Automation;

use Db;

/**
 * Class AutomationRepository
 */
class AutomationRepository
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
     * @param int $automationId
     */
    public function deleteAutomationSettings($automationId)
    {
        if (empty($automationId)) {
            return;
        }

        $sql = '
        DELETE FROM 
            ' . _DB_PREFIX_ . 'getresponse_automation 
        WHERE 
            `id` = ' . (int) $automationId;

        $this->db->execute($sql);
    }

    /**
     * @param bool $isActive
     * @return Automation[]
     */
    public function getAutomation($isActive = false)
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

        $results = [];

        if ($rows = $this->db->ExecuteS($sql)) {
            foreach ($rows as $row) {
                $results[] = AutomationFactory::createFormDb($row);
            }
        }

        return $results;
    }

    /**
     * @param int $categoryId
     * @param int $automationId
     * @param int $contactListId
     * @param string $action
     * @param int $cycleDay
     */
    public function updateAutomation($categoryId, $automationId, $contactListId, $action, $cycleDay)
    {
        $query = '
        UPDATE
            ' . _DB_PREFIX_ . 'getresponse_automation
        SET
            `category_id` = "' . pSQL($categoryId) . '",
            `campaign_id` = "' . pSQL($contactListId). '",
            `action` = "' . pSQL($action) . '",
            `cycle_day` = "' . pSQL($cycleDay) . '"
        WHERE
            `id` = ' . (int)$automationId . '
            AND `id_shop` = ' . (int) $this->idShop;

        $this->db->execute($query);
    }

    /**
     * @param int $categoryId
     * @param int $contactListId
     * @param string $action
     * @param int $cycleDay
     */
    public function addAutomation($categoryId, $contactListId, $action, $cycleDay)
    {
        $query = '
        INSERT INTO ' . _DB_PREFIX_ . 'getresponse_automation (
            `category_id`, 
            `campaign_id`, 
            `action`, 
            `cycle_day`, 
            `id_shop`, 
            `active` 
       ) VALUES (
            "' . pSQL($categoryId) . '",
            "' . pSQL($contactListId) . '",
            "' . pSQL($action) . '",
            "' . pSQL($cycleDay) . '",
            "' . (int)$this->idShop . '",
            "yes"
       )';

        $this->db->execute($query);

    }
}