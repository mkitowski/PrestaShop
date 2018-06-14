<?php
namespace GetResponse\Automation;

/**
 * Class Automation
 * @package GetResponse\Automation
 */
class Automation
{
    /** @var int */
    private $id;

    /** @var int */
    private $shopId;

    /** @var int */
    private $categoryId;

    /** @var string */
    private $contactListId;

    /** @var string */
    private $action;

    /** @var int */
    private $dayOfCycle;

    /** @var string */
    private $status;

    /**
     * @param int $id
     * @param int $shopId
     * @param int $categoryId
     * @param string $contactListId
     * @param string $action
     * @param int $dayOfCycle
     * @param string $status
     */
    public function __construct($id, $shopId, $categoryId, $contactListId, $action, $dayOfCycle, $status)
    {
        $this->id = $id;
        $this->shopId = $shopId;
        $this->categoryId = $categoryId;
        $this->contactListId = $contactListId;
        $this->action = $action;
        $this->dayOfCycle = $dayOfCycle;
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
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
     * @return int
     */
    public function getDayOfCycle()
    {
        return $this->dayOfCycle;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

}