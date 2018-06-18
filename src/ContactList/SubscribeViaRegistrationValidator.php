<?php
namespace GetResponse\ContactList;

use Translate;

/**
 * Class SubscribeViaRegistrationValidator
 * @package GetResponse\ContactList
 */
class SubscribeViaRegistrationValidator
{
    /** @var array */
    private $errors;

    /** @var SubscribeViaRegistrationDto */
    private $subscribeViaRegistrationDto;

    /**
     * @param SubscribeViaRegistrationDto $subscribeViaRegistrationDto
     */
    public function __construct(SubscribeViaRegistrationDto $subscribeViaRegistrationDto)
    {
        $this->subscribeViaRegistrationDto = $subscribeViaRegistrationDto;
        $this->errors = [];
        $this->validate();
    }

    private function validate()
    {
        if (empty($this->subscribeViaRegistrationDto->getContactList()) && $this->subscribeViaRegistrationDto->isSubscriptionEnabled()) {
            $this->errors[] = Translate::getAdminTranslation('You need to select list');

            return;
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}