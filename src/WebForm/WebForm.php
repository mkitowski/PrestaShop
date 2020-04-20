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

namespace GetResponse\WebForm;

/**
 * Class WebForm
 * @package GetResponse\WebForm
 */
class WebForm
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const SIDEBAR_DEFAULT = 'home';

    const STYLE_PRESTASHOP = 'prestashop';
    const STYLE_DEFAULT = 'webform';

    /** @var string */
    private $status;

    /** @var string */
    private $id;

    /** @var string */
    private $sidebar;

    /** @var string */
    private $style;

    /** @var string */
    private $url;

    /**
     * @param string $status
     * @param string $id
     * @param string $sidebar
     * @param string $style
     * @param string $url
     */
    public function __construct($status, $id, $sidebar, $style = self::STYLE_DEFAULT, $url = '')
    {
        $this->status = $status;
        $this->id = $id;
        $this->sidebar = $sidebar;
        $this->style = $style;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
    public function getSidebar()
    {
        return $this->sidebar;
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return self::STATUS_ACTIVE === $this->status;
    }

    /**
     * @param string $sidebar
     * @return bool
     */
    public function hasSamePosition($sidebar)
    {
        return $sidebar === $this->sidebar;
    }

    /**
     * @return bool
     */
    public function hasPrestashopStyle()
    {
        return self::STYLE_PRESTASHOP === $this->style;
    }

    /**
     * @return WebForm
     */
    public static function createEmptyInstance()
    {
        return new self(self::STATUS_INACTIVE, '', '', '', '');
    }

    /**
     * @param array $params
     * @return WebForm
     */
    public static function createFromPost($params)
    {
        if ($params['subscription']) {
            return new self(
                self::STATUS_ACTIVE,
                $params['form'],
                $params['position'],
                $params['style']
            );
        } else {
            return self::createEmptyInstance();
        }
    }
}
