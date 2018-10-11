<?php

/**
 * Class Category
 */
class Category
{
    /** @var int */
    public $id;

    /** @var int */
    public $id_parent;

    /** @var array */
    private $category;

    /**
     * @param array $category
     */
    public function __construct(array $category)
    {
        $this->category = $category;
        $this->id = $category['id'];
        $this->id_parent = $category['id_parent'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->category['name'];
    }
}