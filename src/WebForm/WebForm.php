<?php
namespace GetResponse\WebForm;

/**
 * Class WebForm
 * @package GetResponse\WebForm
 */
class WebForm
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';

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
        return self::ACTIVE === $this->status;
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
        return new self(self::INACTIVE, '', '', '', '');
    }

    /**
     * @param array $params
     * @return WebForm
     */
    public static function createFromPost($params)
    {
        if ($params['subscription']) {
            return new self(
                self::ACTIVE,
                $params['form'],
                $params['position'],
                $params['style']
            );
        } else {
            return new self(
                self::INACTIVE,
                null,
                null,
                null
            );
        }
    }
}
