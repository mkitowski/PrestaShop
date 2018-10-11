<?php
namespace GetResponse\WebTracking;

use GrShareCode\TrackingCode\TrackingCodeService;
use GrShareCode\GetresponseApiException;
use PrestaShopDatabaseException;

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
     * @throws GetresponseApiException
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
     * @throws PrestaShopDatabaseException
     */
    public function getWebTracking()
    {
        return $this->repository->getWebTracking();
    }

}