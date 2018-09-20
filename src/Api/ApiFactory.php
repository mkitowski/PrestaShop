<?php
namespace GetResponse\Api;

use Getresponse;
use GetResponse\Account\AccountDto;
use GetResponse\Account\AccountSettings;
use GrShareCode\Api\ApiKeyAuthorization;
use GrShareCode\Api\ApiType;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\Api\UserAgentHeader;
use GrShareCode\GetresponseApi;

/**
 * Class ApiFactory
 * @package GetResponse\Api
 */
class ApiFactory
{
    /**
     * @param AccountSettings $settings
     * @return GetresponseApi
     * @throws ApiTypeException
     */
    public static function createFromSettings(AccountSettings $settings)
    {
        $authorization = new ApiKeyAuthorization(
            $settings->getApiKey(),
            $settings->getAccountType(),
            $settings->getDomain()
        );

        $userAgentHeader = new UserAgentHeader('PrestaShop', _PS_VERSION_, Getresponse::VERSION);

        return new GetresponseApi($authorization, Getresponse::X_APP_ID, $userAgentHeader);
    }

    /**
     * @param AccountDto $accountDto
     * @return GetresponseApi
     * @throws ApiTypeException
     */
    public static function createFromAccountDto(AccountDto $accountDto)
    {
        $authorization = new ApiKeyAuthorization(
            $accountDto->getApiKey(),
            $accountDto->getAccountType(),
            $accountDto->getDomain()
        );

        $userAgentHeader = new UserAgentHeader('PrestaShop', _PS_VERSION_, Getresponse::VERSION);

        return new GetresponseApi($authorization, Getresponse::X_APP_ID, $userAgentHeader);
    }

}