<?php
namespace GetResponse\Account;

/**
 * Class AccountStatus
 * @package GetResponse\Account
 */
class AccountStatus
{
    /** @var AccountSettingsRepository */
    private $repository;

    /**
     * @param AccountSettingsRepository $accountSettingsRepository
     */
    public function __construct(AccountSettingsRepository $accountSettingsRepository)
    {
        $this->repository = $accountSettingsRepository;
    }

    /**
     * @return bool
     */
    public function isConnectedToGetResponse()
    {
        $settings = $this->repository->getSettings();

        if (!$settings) {
            return false;
        }

        return $settings->isConnectedWithGetResponse();
    }
}