<?php
namespace GetResponse\Contact;

/**
 * Class ContactDtoFactory
 * @package GetResponse\Contact
 */
class ContactDtoFactory
{
    /**
     * @param \stdClass $contact
     * @return ContactDto
     */
    public static function createFromForm($contact)
    {
        return new ContactDto(
            $contact->email,
            $contact->firstname . ' ' . $contact->lastname,
            (bool)isset($contact->newsletter) ? $contact->newsletter : false,
            (array)$contact
        );
    }
}