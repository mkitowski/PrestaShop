<?php
namespace GetResponse\Tests\Unit\WebTracking;

use GetResponse\Tests\Unit\BaseTestCase;
use GetResponse\WebTracking\WebTracking;
use GetResponse\WebTracking\WebTrackingRepository;
use GetResponse\WebTracking\WebTrackingService;
use GrShareCode\TrackingCode\TrackingCodeService as GrTrackingCodeService;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class WebTrackingServiceTest
 * @package GetResponse\Tests\Unit\WebTracking
 */
class WebTrackingServiceTest extends BaseTestCase
{

    /** @var WebTrackingService */
    private $sut;

    /** @var WebTrackingRepository | PHPUnit_Framework_MockObject_MockObject */
    private $repository;

    /** @var GrTrackingCodeService | PHPUnit_Framework_MockObject_MockObject*/
    private $grTrackingCodeService;

    /**
     * @test
     */
    public function shouldUpdateTrackingAsEnabled()
    {
        $status = 'active';
        $trackingCodeSnippet = 'snippet';

        $this->grTrackingCodeService
            ->expects(self::once())
            ->method('getTrackingCode')
            ->willReturn(new WebTracking('status', $trackingCodeSnippet));

        $this->repository
            ->expects(self::once())
            ->method('updateWebTracking')
            ->with(new WebTracking($status, $trackingCodeSnippet));


        $this->sut->saveTracking(new WebTracking($status));
    }

    /**
     * @test
     */
    public function shouldUpdateTrackingAsDisabled()
    {
        $this->repository
            ->expects(self::once())
            ->method('clearWebTracking');

        $this->sut->saveTracking(new WebTracking(WebTracking::TRACKING_INACTIVE));
    }

    /**
     * @test
     */
    public function shouldReturnWebTrackingOrNull()
    {
        $webTracking = new WebTracking('status', 'snippet');

        $this->repository
            ->expects(self::exactly(2))
            ->method('getWebTracking')
            ->willReturn($webTracking, null);

        $this->assertSame($webTracking, $this->sut->getWebTracking());
        $this->assertNull($this->sut->getWebTracking());
    }

    protected function setUp()
    {
        $this->repository = $this->getMockWithoutConstructing(WebTrackingRepository::class);
        $this->grTrackingCodeService = $this->getMockWithoutConstructing(GrTrackingCodeService::class);
        $this->sut = new WebTrackingService($this->repository, $this->grTrackingCodeService);
    }
}
