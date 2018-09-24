<?php
namespace GetResponse\Account;

/**
 * Class AccountSettings
 * @package GetResponse\Account
 */
class AccountSettings
{
    const SUBSCRIPTION_ACTIVE_YES = 'yes';
    const SUBSCRIPTION_ACTIVE_NO = 'no';

    const NEWSLETTER_SUBSCRIPTION_ACTIVE_YES = 'yes';
    const NEWSLETTER_SUBSCRIPTION_ACTIVE_NO = 'no';

    const TRACKING_ACTIVE = 'yes';
    const TRACKING_INACTIVE = 'no';
    const TRACKING_DISABLED = 'disabled';

    const UPDATE_ADDRESS_YES = 'yes';
    const UPDATE_ADDRESS_NO = 'no';

    const ACCOUNT_TYPE_SMB = 'smb';
    const ACCOUNT_TYPE_360_US = '360en';
    const ACCOUNT_TYPE_360_PL = '360pl';

    /** @var int */
    private $id;

    /** @var int */
    private $shopId;

    /** @var string */
    private $apiKey;

    /** @var string */
    private $active_subscription;

    /** @var string */
    private $active_newsletter_subscription;

    /** @var string */
    private $active_tracking;

    /** @var string */
    private $tracking_snippet;

    /** @var string */
    private $update_address;

    /** @var string */
    private $contactListId;

    /** @var string */
    private $cycle_day;

    /** @var string */
    private $account_type;

    /** @var string */
    private $domain;

    /**
     * @param int $id
     * @param int $shopId
     * @param string $apiKey
     * @param string $active_subscription
     * @param string $active_newsletter_subscription
     * @param string $active_tracking
     * @param string $tracking_snippet
     * @param string $update_address
     * @param string $contactListId
     * @param string $cycle_day
     * @param string $account_type
     * @param string $domain
     */
    public function __construct(
        $id,
        $shopId,
        $apiKey,
        $active_subscription,
        $active_newsletter_subscription,
        $active_tracking,
        $tracking_snippet,
        $update_address,
        $contactListId,
        $cycle_day,
        $account_type,
        $domain
    ) {
        $this->id = $id;
        $this->shopId = $shopId;
        $this->apiKey = $apiKey;
        $this->active_subscription = $active_subscription;
        $this->active_newsletter_subscription = $active_newsletter_subscription;
        $this->active_tracking = $active_tracking;
        $this->tracking_snippet = $tracking_snippet;
        $this->update_address = $update_address;
        $this->contactListId = $contactListId;
        $this->cycle_day = $cycle_day;
        $this->account_type = $account_type;
        $this->domain = $domain;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @return string
     */
    public function getActiveNewsletterSubscription()
    {
        return $this->active_newsletter_subscription;
    }

    /**
     * @return string
     */
    public function getTrackingSnippet()
    {
        return $this->tracking_snippet;
    }

    /**
     * @return string
     */
    public function getUpdateAddress()
    {
        return $this->update_address;
    }

    /**
     * @return string
     */
    public function getContactListId()
    {
        return $this->contactListId;
    }

    /**
     * @return string
     */
    public function getCycleDay()
    {
        return $this->cycle_day;
    }

    /**
     * @return string
     */
    public function getAccountType()
    {
        return $this->account_type;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return bool
     */
    public function isTrackingDisabled()
    {
        return $this->getActiveTracking() === self::TRACKING_DISABLED;
    }

    /**
     * @return string
     */
    public function getActiveTracking()
    {
        return $this->active_tracking;
    }

    /**
     * @return bool
     */
    public function isTrackingActive()
    {
        return $this->getActiveTracking() === self::TRACKING_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isSubscriptionActive()
    {
        return $this->getActiveSubscription() === self::SUBSCRIPTION_ACTIVE_YES;
    }

    /**
     * @return string
     */
    public function getActiveSubscription()
    {
        return $this->active_subscription;
    }

    /**
     * @return bool
     */
    public function isConnectedWithGetResponse()
    {
        return !empty($this->getApiKey());
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getHiddenApiKey()
    {
        if (strlen($this->getApiKey()) > 0) {
            return str_repeat('*', strlen($this->getApiKey()) - 6) . substr($this->getApiKey(), -6);
        }

        return $this->getApiKey();
    }

    /**
     * @return bool
     */
    public function isUpdateContactEnabled()
    {
        return self::UPDATE_ADDRESS_YES === $this->update_address;
    }

    /**
     * @return bool
     */
    public function isNewsletterSubscriptionOn()
    {
        return self::NEWSLETTER_SUBSCRIPTION_ACTIVE_YES === $this->active_newsletter_subscription;
    }

    /**
     * @return bool
     */
    public function canSubscriberBeSend()
    {
        return $this->isSubscriptionActive() && !empty($this->getContactListId()) && $this->isNewsletterSubscriptionOn();
    }
}