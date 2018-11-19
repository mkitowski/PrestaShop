<?php
namespace GetResponse\Settings\Registration;

/**
 * Class RegistrationSettings
 * @package GetResponse\Settings\Registration
 */
class RegistrationSettings
{
    const YES = 'yes';
    const NO = 'no';
    const ACTIVE = 'active';

    /** @var bool */
    private $isActive;

    /** @var bool */
    private $isNewsletterActive;

    /** @var string */
    private $listId;

    /** @var int */
    private $cycleDay;

    /** @var bool */
    private $isUpdateContactEnabled;

    /**
     * @param bool $isActive
     * @param bool $isNewsletterActive
     * @param string $listId
     * @param int $cycleDay
     * @param bool $isUpdateContactEnabled
     */
    public function __construct($isActive, $isNewsletterActive, $listId, $cycleDay, $isUpdateContactEnabled)
    {
        $this->isActive = $isActive;
        $this->isNewsletterActive = $isNewsletterActive;
        $this->listId = $listId;
        $this->cycleDay = $cycleDay;
        $this->isUpdateContactEnabled = $isUpdateContactEnabled;
    }

    /**
     * @param array $params
     * @return RegistrationSettings
     */
    public static function createFromPost($params)
    {
        $subscription = $params['subscriptionSwitch'] ? self::YES : self::NO;
        $updateContact = $params['contactInfo'] ? self::YES : self::NO;
        $cycleDay = $params['addToCycle'] ? $params['cycledays'] : null;
        $newsletterSubscribers = $params['newsletter'] ? self::YES : self::NO;

        return new self(
            $subscription,
            $newsletterSubscribers,
            $params['campaign'],
            $cycleDay,
            $updateContact
        );
    }

    /**
     * @param array $params
     * @return RegistrationSettings
     */
    public static function createFromOldDbTable($params)
    {
        return new self(
            $params['active_subscription'],
            $params['active_newsletter_subscription'],
            $params['campaign_id'],
            $params['cycle_day'],
            $params['update_address']
        );
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return bool
     */
    public function isNewsletterActive()
    {
        return $this->isNewsletterActive;
    }

    /**
     * @return string
     */
    public function getListId()
    {
        return $this->listId;
    }

    /**
     * @return int
     */
    public function getCycleDay()
    {
        return $this->cycleDay;
    }

    /**
     * @return bool
     */
    public function isUpdateContactEnabled()
    {
        return $this->isUpdateContactEnabled;
    }

    /**
     * @param array $configuration
     * @return RegistrationSettings
     */
    public static function createFromConfiguration($configuration)
    {
        return new self(
            $configuration['active_subscription'] === self::YES,
            $configuration['active_newsletter_subscription'] === self::YES,
            $configuration['campaign_id'],
            $configuration['cycle_day'],
            $configuration['update_address'] === self::YES
        );
    }

    /**
     * @return RegistrationSettings
     */
    public static function createEmptyInstance()
    {
        return new self(false, false, '', 0, false);
    }
}
