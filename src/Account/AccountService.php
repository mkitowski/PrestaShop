<?php
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
