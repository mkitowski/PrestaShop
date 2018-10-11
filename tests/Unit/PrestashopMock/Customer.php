<?php

/**
 * Class Customer
 */
class Customer
{
    /** @var string */
    public $email;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $params = CustomerParams::getCustomerById($id);
        $this->email = $params['email'];
    }
}