<?php
namespace GetResponse\ContactList;

use Db;

/**
 * Class ContactListRepository
 * @package GetResponse\ContactList
 */
class ContactListRepository
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

}