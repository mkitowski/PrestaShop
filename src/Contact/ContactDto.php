<?php
namespace GetResponse\Contact;

/**
 * Class ContactDto
 * @package GetResponse\Contact
 */
class ContactDto
{
    /** @var string */
    private $email;

    /** @var string */
    private $name;

    /** @var bool */
    private $newsletter;

    /** @var array */
    private $customFields;

    /**
     * @param string $email
     * @param string $name
     * @param bool $newsletter
     * @param array $customFields
     */
    public function __construct($email, $name, $newsletter, array $customFields)
    {
        $this->email = $email;
        $this->name = $name;
        $this->newsletter = $newsletter;
        $this->customFields = $customFields;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        return $this->customFields;
    }
}