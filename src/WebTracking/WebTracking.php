<?php
namespace GetResponse\WebTracking;

/**
 * Class WebTracking
 * @package GetResponse\WebTracking
 */
class WebTracking
{
    const TRACKING_ACTIVE = 'yes';
    const TRACKING_INACTIVE = 'no';
    const TRACKING_DISABLED = 'disabled';

    /** @var string */
    private $status;

    /** @var string */
    private $snippet;

    /**
     * @param string $snippet
     * @param string $status
     */
    public function __construct($status, $snippet)
    {
        $this->status = $status;
        $this->snippet = $snippet;
    }

    /**
     * @return bool
     */
    public function isTrackingDisabled()
    {
        return $this->getStatus() === self::TRACKING_DISABLED;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getSnippet()
    {
        return $this->snippet;
    }

    /**
     * @return bool
     */
    public function isTrackingActive()
    {
        return $this->getStatus() === self::TRACKING_ACTIVE;
    }


}