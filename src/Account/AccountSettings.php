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
    const ACCOUNT_TYPE_360_US = 'mx_us';
    const ACCOUNT_TYPE_360_PL = 'mx_pl';

    /** @var int */
    private $id;

    /** @var int */
    private $shopId;

    /** @var string */
    private $apiKey;

    /** @var string */
    private $activeSubscription;

    /** @var string */
    private $activeNewsletterSubscription;

    /** @var string */
    private $activeTracking;

    /** @var string */
    private $trackingSnippet;

    /** @var string */
    private $updateAddress;

    /** @var string */
    private $contactListId;

    /** @var string */
    private $cycleDay;

    /** @var string */
    private $accountType;

    /** @var string */
    private $domain;

    /**
     * @param int $id
     * @param int $shopId
     * @param string $apiKey
     * @param string $activeSubscription
     * @param string $activeNewsletterSubscription
     * @param string $activeTracking
     * @param string $trackingSnippet
     * @param string $updateAddress
     * @param string $contactListId
     * @param string $cycleDay
     * @param string $accountType
     * @param string $domain
     */
    public function __construct(
        $id,
        $shopId,
        $apiKey,
        $activeSubscription,
        $activeNewsletterSubscription,
        $activeTracking,
        $trackingSnippet,
        $updateAddress,
        $contactListId,
        $cycleDay,
        $accountType,
        $domain
    ) {
        $this->id = $id;
        $this->shopId = $shopId;
        $this->apiKey = $apiKey;
        $this->activeSubscription = $activeSubscription;
        $this->activeNewsletterSubscription = $activeNewsletterSubscription;
        $this->activeTracking = $activeTracking;
        $this->trackingSnippet = $trackingSnippet;
        $this->updateAddress = $updateAddress;
        $this->contactListId = $contactListId;
        $this->cycleDay = $cycleDay;
        $this->accountType = $accountType;
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
        return $this->activeNewsletterSubscription;
    }

    /**
     * @return string
     */
    public function getTrackingSnippet()
    {
        return $this->trackingSnippet;
    }

    /**
     * @return string
     */
    public function getUpdateAddress()
    {
        return $this->updateAddress;
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
        return $this->cycleDay;
    }

    /**
     * @return string
     */
    public function getAccountType()
    {
        return $this->accountType;
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
        return self::TRACKING_DISABLED === $this->getActiveTracking();
    }

    /**
     * @return string
     */
    public function getActiveTracking()
    {
        return $this->activeTracking;
    }

    /**
     * @return bool
     */
    public function isTrackingActive()
    {
        return self::TRACKING_ACTIVE === $this->getActiveTracking();
    }

    /**
     * @return bool
     */
    public function isSubscriptionActive()
    {
        return self::SUBSCRIPTION_ACTIVE_YES === $this->getActiveSubscription();
    }

    /**
     * @return string
     */
    public function getActiveSubscription()
    {
        return $this->activeSubscription;
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
     * @return null|string
     */
    public function getHiddenApiKey()
    {
        if (strlen($this->getApiKey()) > 0) {
            return str_repeat('*', strlen($this->getApiKey()) - 6) . substr($this->getApiKey(), -6);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isUpdateContactEnabled()
    {
        return self::UPDATE_ADDRESS_YES === $this->updateAddress;
    }

    /**
     * @return bool
     */
    public function isNewsletterSubscriptionOn()
    {
        return self::NEWSLETTER_SUBSCRIPTION_ACTIVE_YES === $this->activeNewsletterSubscription;
    }

    /**
     * @return bool
     */
    public function canSubscriberBeSend()
    {
        return $this->isSubscriptionActive() && !empty($this->getContactListId());
    }
}