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

namespace GetResponse\Account;

use GrShareCode\Account\Account;
use GrShareCode\Account\AccountService as GrAccountService;
use GrShareCode\Api\Exception\GetresponseApiException;

/**
 * Class AccountService
 * @package GetResponse\Account
 */
class AccountService
{
    /** @var GrAccountService */
    private $grAccountService;

    /** @var AccountSettingsRepository */
    private $repository;

    /**
     * @param GrAccountService $grAccountService
     * @param AccountSettingsRepository $accountSettingsRepository
     */
    public function __construct(
        GrAccountService $grAccountService,
        AccountSettingsRepository $accountSettingsRepository
    ) {
        $this->grAccountService = $grAccountService;
        $this->repository = $accountSettingsRepository;
    }

    /**
     * @return Account
     * @throws GetresponseApiException
     */
    public function getAccountDetails()
    {
        return $this->grAccountService->getAccount();
    }

    /**
     * @return bool
     */
    public function isConnectedToGetResponse()
    {
        return !empty($this->repository->getSettings()->getApiKey());
    }

    public function disconnectFromGetResponse()
    {
        $this->repository->clearConfiguration();
    }

    /**
     * @return bool
     */
    public function isConnectionAvailable()
    {
        return $this->grAccountService->isConnectionAvailable();
    }

    /**
     * @param string $apiKey
     * @param string $accountType
     * @param string $domain
     */
    public function updateApiSettings($apiKey, $accountType, $domain)
    {
        $this->repository->updateApiSettings($apiKey, $accountType, $domain);
    }

    /**
     * @return AccountSettings
     */
    public function getAccountSettings()
    {
        return $this->repository->getSettings();
    }
}
