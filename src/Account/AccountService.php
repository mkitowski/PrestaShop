<?php
namespace GetResponse\Account;

use GetResponse\Settings\Settings;
use GetResponse\Settings\SettingsService;
use GrShareCode\Account\Account;
use GrShareCode\Account\AccountService as GrAccountService;
use GrShareCode\GetresponseApiException;
use GrShareCode\TrackingCode\TrackingCodeService;

/**
 * Class AccountService
 * @package GetResponse\Account
 */
class AccountService
{
    /** @var GrAccountService */
    private $grAccountService;

    /** @var SettingsService */
    private $settingsService;

    /** @var TrackingCodeService */
    private $trackingCodeService;

    /**
     * @param GrAccountService $grAccountService
     * @param SettingsService $settingsService
     * @param TrackingCodeService $trackingCodeService
     */
    public function __construct(
        GrAccountService $grAccountService,
        SettingsService $settingsService,
        TrackingCodeService $trackingCodeService
    ) {
        $this->grAccountService = $grAccountService;
        $this->settingsService = $settingsService;
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
     * @return Settings
     */
    public function getSettings()
    {
        return $this->settingsService->getSettings();
    }

    /**
     * @return bool
     */
    public function isConnectedToGetResponse()
    {
        return !empty($this->settingsService->getSettings()->getApiKey());
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->settingsService->getSettings()->getApiKey();
    }

    /**
     * @return string
     */
    public function getActiveTracking()
    {
        return $this->settingsService->getSettings()->getActiveTracking();
    }

    public function disconnectFromGetResponse()
    {
        $this->settingsService->disconnectFromGetResponse();
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
        $trackingCode = $this->trackingCodeService->getTrackingCode();
        $trackingStatus = $trackingCode->isFeatureEnabled() ? Settings::TRACKING_INACTIVE : Settings::TRACKING_DISABLED;
        $this->settingsService->updateTracking($trackingStatus, $trackingCode->getSnippet());

        $this->settingsService->updateApiSettings($apiKey, $accountType, $domain);
    }
}