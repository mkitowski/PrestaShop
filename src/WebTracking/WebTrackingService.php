<?php
namespace GetResponse\WebTracking;

use GrShareCode\TrackingCode\TrackingCodeService;
use GrShareCode\Api\Exception\GetresponseApiException;

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
     * @param string $status
     * @throws GetresponseApiException
     */
    public function saveTracking($status)
    {
        $trackingCode = $this->trackingCodeService->getTrackingCode();
        $this->repository->saveTracking(new WebTracking($status, $trackingCode->getSnippet()));
    }

    /**
     * @return WebTracking
     */
    public function getWebTracking()
    {
        return $this->repository->getWebTracking();
    }
}
