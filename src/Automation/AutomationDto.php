<?php
namespace GetResponse\Automation;

/**
 * Class AutomationDto
 * @package GetResponse\Automation
 */
class AutomationDto
{
    /** @var string */
    private $id;

    /** @var string */
    private $category;

    /** @var string */
    private $contactListId;

    /** @var string */
    private $action;

    /** @var string */
    private $addToCycle;

    /** @var string */
    private $cycleDay;

    /**
     * @param string $id
     * @param string $category
     * @param string $contactListId
     * @param string $action
     * @param string $addToCycle
     * @param string $cycleDay
     */
    public function __construct($id, $category, $contactListId, $action, $addToCycle, $cycleDay)
    {
        $this->id = $id;
        $this->category = $category;
        $this->contactListId = $contactListId;
        $this->action = $action;
        $this->addToCycle = $addToCycle;
        $this->cycleDay = !empty($addToCycle) ? $cycleDay : null;
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
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function getContactListId()
    {
        return $this->contactListId;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getAddToCycle()
    {
        return $this->addToCycle;
    }

    /**
     * @return string
     */
    public function getCycleDay()
    {
        return $this->cycleDay;
    }

    /**
     * @return bool
     */
    public function hasId()
    {
        return !empty($this->getId());
    }

}