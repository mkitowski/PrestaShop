<?php

namespace GetResponse\WebTracking;

use Configuration;

/**
 * Class WebTrackingRepository
 * @package GetResponse\WebTracking
 */
class WebTrackingRepository
{
    const WEB_TRACKING_KEY = 'getresponse_web_tracking';
    const TRACKING_CODE_KEY = 'getresponse_tracking_code';

    /**
     * @return WebTracking|null
     */
    public function getWebTracking()
    {
        $status = json_decode(Configuration::get(self::WEB_TRACKING_KEY), true);

        if (empty($status)) {
            return WebTracking::createEmptyInstance();
        }

        return new WebTracking($status['status'], Configuration::get(self::TRACKING_CODE_KEY));
    }

    /**
     * @param WebTracking $webTracking
     */
    public function updateWebTracking(WebTracking $webTracking)
    {
        Configuration::updateValue(
            self::WEB_TRACKING_KEY,
            json_encode(['status' => $webTracking->getStatus()]),
            true
        );

        Configuration::updateValue(self::TRACKING_CODE_KEY, $webTracking->getSnippet(), true);
    }

    public function clearWebTracking()
    {
        Configuration::updateValue(self::WEB_TRACKING_KEY, NULL);
        Configuration::updateValue(self::TRACKING_CODE_KEY, NULL);
    }
}
