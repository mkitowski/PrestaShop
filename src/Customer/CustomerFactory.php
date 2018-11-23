<?php
namespace GetResponse\Customer;

use \Customer as PsCustomer;

/**
 * Class CustomerFactory
 * @package GetResponse\Customer
 */
class CustomerFactory
{
    /**
     * @param array $params
     * @return Customer
     */
    public static function createFromArray($params)
    {
        return new Customer(
            $params['id'],
            $params['firstname'],
            $params['lastname'],
            $params['birthday'],
            $params['address'],
            $params['postal'],
            $params['company'],
            $params['country'],
            $params['city'],
            $params['phone'],
            $params['email']
        );
    }

    /**
     * @param string $email
     * @return Customer
     */
    public static function createFromNewsletter($email)
    {
        return new Customer(
            0,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $email
        );
    }

    /**
     * @param $customer
     * @return Customer
     */
    public static function createFromPsCustomerObject(PsCustomer $customer)
    {
        return new Customer(
            $customer->id,
            $customer->firstname,
            $customer->lastname,
            $customer->birthday,
            null,
            null,
            null,
            null,
            null,
            null,
            $customer->email
        );
    }
}
