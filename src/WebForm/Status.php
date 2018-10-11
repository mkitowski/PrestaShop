<?php
namespace GetResponse\WebForm;

/**
 * Class ActiveSubscription
 * @package GetResponse\WebForm
 */
class Status
{
    const SUBSCRIPTION_ACTIVE = 'yes';
    const SUBSCRIPTION_INACTIVE = 'no';

    /** @var int */
    private $subscription;

    /**
     * @param int $subscription
     */
    public function __construct($subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * @param int $subscription
     * @return Status
     */
    public static function fromRequest($subscription)
    {
        return new self($subscription === '1' ? self::SUBSCRIPTION_ACTIVE : self::SUBSCRIPTION_INACTIVE);
    }

    /**
     * @return int
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->subscription === self::SUBSCRIPTION_ACTIVE;
    }
}