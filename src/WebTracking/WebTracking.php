<?php
namespace GetResponse\WebTracking;

use GetResponse\Settings\Settings;

/**
 * Class WebTracking
 */
class WebTracking
{
    const TRACKING_ON = '1';
    const TRACKING_OFF = '0';

    /** @var string */
    private $tracking;

    /**
     * @param string $tracking
     */
    public function __construct($tracking)
    {
        $this->tracking = $tracking;
    }

    /**
     * @return string
     */
    public function getTracking()
    {
        return $this->tracking;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getTracking() === self::TRACKING_ON;
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return $this->getTracking() === self::TRACKING_OFF;
    }

    /**
     * @return string
     */
    public function toSettings()
    {
        return $this->getTracking() === self::TRACKING_ON ? Settings::TRACKING_ACTIVE : Settings::TRACKING_INACTIVE;
    }

}