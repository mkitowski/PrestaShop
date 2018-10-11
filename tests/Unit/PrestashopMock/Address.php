<?php

/**
 * Class Address
 */
class Address
{
    /** @var int */
    public $id_country;

    /** @var string */
    public $country;

    /** @var string */
    public $firstname;

    /** @var string */
    public $lastname;

    /** @var string */
    public $address1;

    /** @var string */
    public $address2;

    /** @var string */
    public $city;

    /** @var string */
    public $postcode;

    /** @var string */
    public $phone;

    /** @var string */
    public $company;

    /**
     * @param int $addressId
     */
    public function __construct($addressId)
    {
        $this->id_country = 4;
        $this->country = 'Poland';
        $this->firstname = 'Adam';
        $this->lastname = 'Kowalski';
        $this->address1 = 'Arkońska 24';
        $this->address2 = 'Building 5';
        $this->city = 'Gdańsk';
        $this->postcode = '81-190';
        $this->phone = '123-123-123';
        $this->company = 'GetResponse';
    }
}