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
    private $isAddressUpdated;

    /**
     * @param bool $isActive
     * @param bool $isNewsletterActive
     * @param string $listId
     * @param int $cycleDay
     * @param bool $isAddressUpdated
     */
    public function __construct($isActive, $isNewsletterActive, $listId, $cycleDay, $isAddressUpdated)
    {
        $this->isActive = $isActive;
        $this->isNewsletterActive = $isNewsletterActive;
        $this->listId = $listId;
        $this->cycleDay = $cycleDay;
        $this->isAddressUpdated = $isAddressUpdated;
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
    public function isAddressUpdated()
    {
        return $this->isAddressUpdated;
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
