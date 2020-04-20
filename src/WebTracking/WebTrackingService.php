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
