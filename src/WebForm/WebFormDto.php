<?php
namespace GetResponse\WebForm;

/**
 * Class WebFormDto
 * @package GetResponse\WebForm
 */
class WebFormDto
{
    const SUBSCRIPTION_ACTIVE = '1';
    const SUBSCRIPTION_INACTIVE = '0';

    /** @var string */
    private $formId;

    /** @var string */
    private $position;

    /** @var string */
    private $style;

    /** @var string */
    private $subscriptionStatus;

    /**
     * @param string $formId
     * @param string $position
     * @param string $style
     * @param string $subscriptionStatus
     */
    public function __construct($formId, $position, $style, $subscriptionStatus)
    {
        $this->formId = $formId;
        $this->position = $position;
        $this->style = $style;
        $this->subscriptionStatus = $subscriptionStatus;
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
    public function getSubscriptionStatus()
    {
        return $this->subscriptionStatus;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getSubscriptionStatus() === self::SUBSCRIPTION_ACTIVE;
    }
}