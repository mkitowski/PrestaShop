<?php

namespace GetResponse\WebTracking;

use Configuration;

/**
 * Class WebTrackingRepository
 * @package GetResponse\WebTracking
 */
class WebTrackingRepository
{
    const RESOURCE_KEY = 'getresponse_web_tracking';

    /**
     * @return WebTracking|null
     */
    public function getWebTracking()
    {
        $status = json_decode(Configuration::get(self::RESOURCE_KEY), true);

        if (empty($status)) {
            return WebTracking::createEmptyInstance();
        }

        return new WebTracking($status['status'], Configuration::get(self::RESOURCE_KEY));
    }

    /**
     * @param WebTracking $webTracking
     */
    public function updateWebTracking(WebTracking $webTracking)
    {
        Configuration::updateValue(
            self::RESOURCE_KEY,
            json_encode(['status' => $webTracking->getStatus()]),
            true
        );

        Configuration::updateValue(self::RESOURCE_KEY, $webTracking->getSnippet(), true);
    }

    public function clearWebTracking()
    {
        Configuration::updateValue(self::RESOURCE_KEY, NULL);
    }

}
