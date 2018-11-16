<?php
namespace GetResponse\Account;

use GrShareCode\Account\Account;
use GrShareCode\Account\AccountService as GrAccountService;
use GrShareCode\Api\Exception\GetresponseApiException;
use GrShareCode\TrackingCode\TrackingCodeService;

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

    /** @var TrackingCodeService */
    private $trackingCodeService;

    /**
     * @param GrAccountService $grAccountService
     * @param AccountSettingsRepository $accountSettingsRepository
     * @param TrackingCodeService $trackingCodeService
     */
    public function __construct(
        GrAccountService $grAccountService,
        AccountSettingsRepository $accountSettingsRepository,
        TrackingCodeService $trackingCodeService
    ) {
        $this->grAccountService = $grAccountService;
        $this->repository = $accountSettingsRepository;
        $this->trackingCodeService = $trackingCodeService;
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
     * @return AccountSettings|null
     */
    public function getAccountSettings()
    {
        return $this->repository->getSettings();
    }
}
