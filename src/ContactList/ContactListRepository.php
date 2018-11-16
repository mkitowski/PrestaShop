<?php
namespace GetResponse\ContactList;

use Configuration;
use ConfigurationSettings;

/**
 * Class ContactListRepository
 * @package GetResponse\ContactList
 */
class ContactListRepository
{
    /**
     * @param string $activeSubscription
     * @param string $campaignId
     * @param string $updateAddress
     * @param string $cycleDay
     * @param string $newsletter
     */
    public function updateSettings($activeSubscription, $campaignId, $updateAddress, $cycleDay, $newsletter)
    {
        Configuration::updateValue(
            ConfigurationSettings::REGISTRATION,
            json_encode([
                'active_subscription' => $activeSubscription,
                'active_newsletter_subscription' => $newsletter,
                'campaign_id' => $campaignId,
                'update_address' => $updateAddress,
                'cycle_day' => $cycleDay
            ])
        );
    }
}
