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

/**
 * Class WebTracking
 * @package GetResponse\WebTracking
 */
class WebTracking
{
    const TRACKING_ACTIVE = 'active';
    const TRACKING_INACTIVE = 'inactive';
    const TRACKING_DISABLED = 'disabled';
    const TRACKING_ON = '1';
    const TRACKING_OFF = '0';

    /** @var string */
    private $status;

    /** @var string */
    private $snippet;

    /**
     * @param string $snippet
     * @param string $status
     */
    public function __construct($status, $snippet = '')
    {
        $this->status = $status;
        $this->snippet = $snippet;
    }

    /**
     * @return bool
     */
    public function isTrackingDisabled()
    {
        return $this->getStatus() === self::TRACKING_DISABLED;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getSnippet()
    {
        return $this->snippet;
    }

    /**
     * @return bool
     */
    public function isTrackingActive()
    {
        return $this->getStatus() === self::TRACKING_ACTIVE;
    }

    /**
     * @return WebTracking
     */
    public static function createEmptyInstance()
    {
        return new self(self::TRACKING_INACTIVE, '');
    }

    /**
     * @param $snippet
     */
    public function setSnippetCode($snippet)
    {
        $this->snippet = $snippet;
    }
}
