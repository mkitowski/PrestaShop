<?php
/**
 * 2007-2020 PrestaShop
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
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace GetResponse\Account;

use Db;
use GetResponse\Api\ApiFactory;
use GetResponseRepository;
use GrShareCode\Account\AccountService as GrAccountService;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\Api\Exception\GetresponseApiException;
use GrShareCode\Api\GetresponseApiClient;
use GetResponse\Helper\Shop as GrShop;

/**
 * Class AccountServiceFactory
 * @package GetResponse\Account
 */
class AccountServiceFactory
{
    /**
     * @return AccountService
     * @throws GetresponseApiException
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository();
        $api = ApiFactory::createFromSettings($accountSettingsRepository->getSettings());
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new AccountService(
            new GrAccountService($apiClient),
            $accountSettingsRepository
        );
    }

    /**
     * @param AccountDto $accountDto
     * @return AccountService
     * @throws ApiTypeException
     */
    public static function createFromAccountDto(AccountDto $accountDto)
    {
        $api = ApiFactory::createFromAccountDto($accountDto);
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new AccountService(
            new GrAccountService($apiClient),
            new AccountSettingsRepository()
        );
    }
}
