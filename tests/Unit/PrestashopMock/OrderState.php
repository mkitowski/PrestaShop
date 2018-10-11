<?php
/**
 * Class OrderState
 * @package Unit\PrestashopMock
 */
class OrderState
{
    /** @var string */
    public $name;

    public function __construct($currentState, $langId)
    {
        $this->name = 'pending';
    }
}