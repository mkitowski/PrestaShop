<?php
namespace GetResponse\WebTracking;

use GetResponse\Settings\SettingsRepository;
use GrShareCode\TrackingCode\TrackingCodeService;

/**
 * Class WebTrackingService
 * @package GetResponse\WebTracking
 */
class WebTrackingService
{
    /** @var WebTrackingRepository */
    private $repository;

    /** @var TrackingCodeService */
    private $trackingCodeService;

    /**
     * @param WebTrackingRepository $repository
     * @param TrackingCodeService $trackingCodeService
     */
    public function __construct(WebTrackingRepository $repository, TrackingCodeService $trackingCodeService)
    {
        $this->repository = $repository;
        $this->trackingCodeService = $trackingCodeService;
    }

    /**
     * @param WebTrackingDto $webTracking
     */
    public function updateTracking(WebTrackingDto $webTracking)
    {
        $trackingCode = $this->trackingCodeService->getTrackingCode();

        $this->repository->updateTracking(
            $webTracking->toSettings(),
            $webTracking->isEnabled() ? $trackingCode->getSnippet() : ''
        );
    }

    /**
     * @return WebTracking|null
     */
    public function getWebTracking()
    {
        return $this->repository->getWebTracking();
    }

}