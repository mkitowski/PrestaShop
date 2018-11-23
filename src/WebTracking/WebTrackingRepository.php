<?php

namespace GetResponse\WebTracking;

use Configuration;
use GetResponse\Config\ConfigurationKeys;

/**
 * Class WebTrackingRepository
 * @package GetResponse\WebTracking
 */
class WebTrackingRepository
{
    /**
     * @return WebTracking|null
     */
    public function getWebTracking()
    {
        $status = json_decode(Configuration::get(ConfigurationKeys::WEB_TRACKING), true);

        if (empty($status)) {
            return WebTracking::createEmptyInstance();
        }

        return new WebTracking($status['status'], Configuration::get(ConfigurationKeys::TRACKING_CODE));
    }

    /**
     * @param WebTracking $webTracking
     */
    public function saveTracking(WebTracking $webTracking)
    {
        Configuration::updateValue(
            ConfigurationKeys::WEB_TRACKING,
            json_encode(['status' => $webTracking->getStatus()]),
            true
        );

        Configuration::updateValue(ConfigurationKeys::TRACKING_CODE, $webTracking->getSnippet(), true);
    }

}
