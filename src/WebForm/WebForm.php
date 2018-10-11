<?php
namespace GetResponse\WebForm;

/**
 * Class WebForm
 * @package GetResponse\WebForm
 */
class WebForm
{
    const STATUS_ACTIVE = 'yes';
    const STATUS_INACTIVE = 'no';

    const SIDEBAR_LEFT = 'left';
    const SIDEBAR_RIGHT = 'right';
    const SIDEBAR_HEADER = 'header';
    const SIDEBAR_TOP = 'top';
    const SIDEBAR_FOOTER = 'footer';
    const SIDEBAR_HOME = 'home';
    const SIDEBAR_DEFAULT = self::SIDEBAR_HOME;

    const STYLE_WEBFORM = 'webform';
    const STYLE_PRESTASHOP = 'prestashop';
    const STYLE_DEFAULT = self::STYLE_WEBFORM;

    /** @var string */
    private $id;

    /** @var string */
    private $status;

    /** @var string */
    private $sidebar;

    /** @var string */
    private $style;

    /** @var string */
    private $url;

    /**
     * @param string $id
     * @param string $status
     * @param string $sidebar
     * @param string $style
     * @param string $url
     */
    public function __construct($id, $status, $sidebar, $style, $url)
    {
        $this->id = $id;
        $this->status = $status;
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
     * @return bool
     */
    public function isStatusActive()
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

}