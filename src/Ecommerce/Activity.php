<?php
namespace GetResponse\Ecommerce;

/**
 * Class Activity
 * @package GetResponse\Ecommerce
 */
class Activity
{
    const ACTIVE = 'yes';
    const INACTIVE = 'no';

    /** @var string */
    private $activity;

    /**
     * @param string $activity
     */
    public function __construct($activity)
    {
        $this->activity = $activity;
    }

    /**
     * @param int $activity
     * @return Activity
     */
    public static function createFromRequest($activity)
    {
        return new self($activity === '1' ? self::ACTIVE : self::INACTIVE);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->activity === self::ACTIVE;
    }

}