<?php
namespace GetResponse\Customer;

/**
 * Class Customer
 * @package GetResponse\Customer
 */
class Customer
{
    /** @var int */
    private $id;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var string */
    private $birthDate;

    /** @var string */
    private $address;

    /** @var string */
    private $postalCode;

    /** @var string */
    private $company;

    /** @var string */
    private $country;

    /** @var string */
    private $city;

    /** @var string */
    private $phone;

    /** @var string */
    private $email;

    /**
     * @param int $id
     * @param string $firstName
     * @param string $lastName
     * @param string $birthDate
     * @param string $address
     * @param string $postalCode
     * @param string $company
     * @param string $country
     * @param string $city
     * @param string $phone
     * @param string $email
     */
    public function __construct($id, $firstName, $lastName, $birthDate, $address, $postalCode, $company, $country, $city, $phone, $email)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->birthDate = $birthDate;
        $this->address = $address;
        $this->postalCode = $postalCode;
        $this->company = $company;
        $this->country = $country;
        $this->city = $city;
        $this->phone = $phone;
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    /**
     * @return string
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $propertyName
     * @return string
     */
    public function getValueByPropertyName($propertyName)
    {
        return isset($this->$propertyName) ? $this->$propertyName : '';
    }
}
