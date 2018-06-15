<?php

use GetResponse\Account\AccountDto;
use GetResponse\Settings\Settings;
use GrShareCode\Api\ApiType;
use GrShareCode\Api\UserAgentHeader;
use GrShareCode\GetresponseApi;

class GrApiFactory
{
//    /**
//     * @param Settings $settings
//     * @return GetresponseApi
//     */
//    public static function createFromSettings(Settings $settings)
//    {
//        $userAgentHeader = new UserAgentHeader('PrestaShop', _PS_VERSION_, Getresponse::VERSION);
//        $type = self::getApiType($settings->getAccountType(), $settings->getDomain());
//
//        return new GetresponseApi($settings->getApiKey(), $type, Getresponse::X_APP_ID, $userAgentHeader);
//    }
//
//    /**
//     * @param AccountDto $accountDto
//     * @return GetresponseApi
//     */
//    public static function createFromAccountDto(AccountDto $accountDto)
//    {
//        $userAgentHeader = new UserAgentHeader('PrestaShop', _PS_VERSION_, Getresponse::VERSION);
//        $type = self::getApiType($accountDto->getAccountType(), $accountDto->getDomain());
//
//        return new GetresponseApi($accountDto->getApiKey(), $type, Getresponse::X_APP_ID, $userAgentHeader);
//    }
//
//    /**
//     * @param string $accountType
//     * @param string $domain
//     * @return ApiType
//     */
//    private static function getApiType($accountType, $domain)
//    {
//        switch ($accountType) {
//            case '360en':
//                $type = ApiType::createForMxUs($domain);
//                break;
//            case '360pl':
//                $type = ApiType::createForMxPl($domain);
//                break;
//            default:
//                $type = ApiType::createForSMB();
//                break;
//        }
//
//        return $type;
//    }
}
