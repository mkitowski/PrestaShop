<?php

use GrShareCode\Api\ApiType;
use GrShareCode\Api\UserAgentHeader;
use GrShareCode\GetresponseApi;

class GrApiFactory
{
    /**
     * @param array $settings
     * @return GetresponseApi
     */
    public static function createFromSettings(array $settings)
    {
        $userAgentHeader = new UserAgentHeader('PrestaShop', _PS_VERSION_, Getresponse::VERSION);
        $type = self::getApiType($settings['account_type'], $settings['crypto']);

        return new GetresponseApi($settings['api_key'], $type, Getresponse::X_APP_ID, $userAgentHeader);
    }

    /**
     * @param string $accountType
     * @param string $crypto
     * @return ApiType
     */
    private static function getApiType($accountType, $crypto)
    {
        switch ($accountType) {
            case '360en':
                $type = ApiType::createForMxUs($crypto);
                break;
            case '360pl':
                $type = ApiType::createForMxPl($crypto);
                break;
            default:
                $type = ApiType::createForSMB();
                break;
        }

        return $type;
    }
}
