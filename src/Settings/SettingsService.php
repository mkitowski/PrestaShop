<?php
namespace GetResponse\Settings;

/**
 * Class Service
 * @package GetResponse\Settings
 */
class SettingsService
{
    /** @var SettingsRepository */
    private $repository;

    /**
     * @param SettingsRepository $repository
     */
    public function __construct(SettingsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Settings
     */
    public function getSettings()
    {
        return $this->repository->getSettings();
    }

}