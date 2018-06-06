<?php

use GrShareCode\Api\ApiType as GrApiType;
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
        $type = self::getApiType($settings['account_type'], $settings['crypto']);

        $userAgentHeader = new UserAgentHeader(
            'PrestaShop',
            _PS_VERSION_,
            Getresponse::VERSION
        );

        return new GetresponseApi($settings['api_key'], $type, Getresponse::X_APP_ID, $userAgentHeader);
    }

    /**
     * @param string $accountType
     * @param string $crypto
     * @return GrApiType
     */
    private static function getApiType($accountType, $crypto)
    {
        switch ($accountType) {
            case '360en':
                $type = GrApiType::createForMxUs($crypto);
                break;
            case '360pl':
                $type = GrApiType::createForMxPl($crypto);
                break;
            default:
                $type = GrApiType::createForSMB();
                break;
        }

        return $type;
    }
}
