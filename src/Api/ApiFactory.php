<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author     Getresponse <grintegrations@getresponse.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace GetResponse\Api;

use Getresponse;
use GetResponse\Account\AccountDto;
use GetResponse\Account\AccountSettings;
use GrShareCode\Api\Authorization\ApiKeyAuthorization;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\Api\UserAgentHeader;
use GrShareCode\Api\GetresponseApi;

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
