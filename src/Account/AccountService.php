<?php
namespace GetResponse\Account;

use GrShareCode\Account\Account;
use GrShareCode\Account\AccountService as GrAccountService;
use GrShareCode\GetresponseApiException;
use GrShareCode\TrackingCode\TrackingCodeService;
use PrestaShopDatabaseException;

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
     * @return AccountSettings
     * @throws PrestaShopDatabaseException
     */
    public function getSettings()
    {
        return $this->repository->getSettings();
    }

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function isConnectedToGetResponse()
    {
        return !empty($this->repository->getSettings()->getApiKey());
    }

    public function disconnectFromGetResponse()
    {
        $this->repository->disconnectApiSettings();
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
     * @throws GetresponseApiException
     */
    public function updateApiSettings($apiKey, $accountType, $domain)
    {
        $trackingCode = $this->trackingCodeService->getTrackingCode();
        $trackingStatus = $trackingCode->isFeatureEnabled()
            ? AccountSettings::TRACKING_INACTIVE
            : AccountSettings::TRACKING_DISABLED;

        $this->repository->updateTracking($trackingStatus, $trackingCode->getSnippet());
        $this->repository->updateApiSettings($apiKey, $accountType, $domain);
    }
}