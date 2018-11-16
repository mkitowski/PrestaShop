<?php
namespace GetResponse\WebTracking;

/**
 * Class WebTrackingException
 * @package GetResponse\WebTracking
 */
class WebTrackingException extends \Exception
{
    /**
     * @param string $status
     * @return WebTrackingException
     */
    public static function createForIncorrectStatus($status)
    {
        return new self('Incorrect status - ' . $status);
    }
}
