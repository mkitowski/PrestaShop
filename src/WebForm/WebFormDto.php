<?php
namespace GetResponse\WebForm;

/**
 * Class WebFormDto
 * @package GetResponse\WebForm
 */
class WebFormDto
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';

    /** @var string */
    private $status;

    /** @var string */
    private $formId;

    /** @var string */
    private $position;

    /** @var string */
    private $style;

    /**
     * @param string $status
     * @param string $formId
     * @param string $position
     * @param string $style
     */
    public function __construct($status, $formId, $position, $style)
    {
        $this->status = $status;
        $this->formId = $formId;
        $this->position = $position;
        $this->style = $style;
    }

    /**
     * @param array $params
     * @return WebFormDto
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

    /**
     * @return string
     */
    public function getFormId()
    {
        return $this->formId;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
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
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getStatus() === self::ACTIVE;
    }
}
