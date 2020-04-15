<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author     Getresponse <grintegrations@getresponse.com>
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

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
