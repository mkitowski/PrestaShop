<?php

namespace GetResponse\WebTracking;

use Configuration;
use ConfigurationSettings;

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
        $status = json_decode(Configuration::get(ConfigurationSettings::WEB_TRACKING), true);

        if (empty($status)) {
            return WebTracking::createEmptyInstance();
        }

        return new WebTracking($status['status'], Configuration::get(ConfigurationSettings::TRACKING_CODE));
    }

    /**
     * @param WebTracking $webTracking
     */
    public function saveTracking(WebTracking $webTracking)
    {
        Configuration::updateValue(
            ConfigurationSettings::WEB_TRACKING,
            json_encode(['status' => $webTracking->getStatus()]),
            true
        );

        Configuration::updateValue(ConfigurationSettings::TRACKING_CODE, $webTracking->getSnippet(), true);
    }

}
