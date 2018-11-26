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
     * @param WebTracking $webTracking
     * @throws GetresponseApiException
     */
    public function saveTracking(WebTracking $webTracking)
    {
        if ($webTracking->isTrackingActive()) {
            $webTracking->setSnippetCode($this->trackingCodeService->getTrackingCode()->getSnippet());
            $this->repository->updateWebTracking($webTracking);
        } else {
            $this->repository->clearWebTracking();
        }
    }

    /**
     * @return WebTracking
     */
    public function getWebTracking()
    {
        return $this->repository->getWebTracking();
    }
}
