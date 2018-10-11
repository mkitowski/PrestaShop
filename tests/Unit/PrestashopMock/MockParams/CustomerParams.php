<?php

/**
 * Class CustomerParams
 */
class CustomerParams
{
    /**
     * @var array
     */
    private static $customer = [
        1 => ['email' => 'customer@getresponse.com']
    ];

    /**
     * @param int $id
     * @return array
     */
    public static function getCustomerById($id)
    {
        return static::$customer[$id];
    }
}