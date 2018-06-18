<?php
namespace GetResponse\ContactList;

/**
 * Class SubscribeViaRegistrationDto
 * @package GetResponse\ContactList
 */
class SubscribeViaRegistrationDto
{
    const SUBSCRIPTION_ENABLED = '1';
    const SUBSCRIPTION_DISABLED = '0';

    const NEWSLETTER_ENABLED = '1';
    const NEWSLETTER_DISABLED = '0';

    const ADD_TO_CYCLE_YES = '1';
    const ADD_TO_CYCLE_NO = '0';

    const UPDATE_CONTACT_ENABLED = '1';
    const UPDATE_CONTACT_DISABLED = '0';

    /** @var string */
    private $subscriptionEnabled;

    /** @var string */
    private $newsletterEnabled;

    /** @var string */
    private $contactList;

    /** @var string */
    private $addToCycle;

    /** @var string */
    private $cycleDay;

    /** @var string */
    private $updateContactEnabled;

    /**
     * @param string $subscriptionEnabled
     * @param string $newsletterEnabled
     * @param string $contactList
     * @param string $addToCycle
     * @param string $cycleDay
     * @param string $updateContactEnabled
     */
    public function __construct(
        $subscriptionEnabled,
        $newsletterEnabled,
        $contactList,
        $addToCycle,
        $cycleDay,
        $updateContactEnabled
    ) {
        $this->subscriptionEnabled = $subscriptionEnabled;
        $this->newsletterEnabled = $newsletterEnabled;
        $this->contactList = $contactList;
        $this->addToCycle = $addToCycle;
        $this->cycleDay = $cycleDay;
        $this->updateContactEnabled = $updateContactEnabled;
    }

    /**
     * @return string
     */
    public function getSubscriptionEnabled()
    {
        return $this->subscriptionEnabled;
    }

    /**
     * @return string
     */
    public function getNewsletterEnabled()
    {
        return $this->newsletterEnabled;
    }

    /**
     * @return string
     */
    public function getContactList()
    {
        return $this->contactList;
    }

    /**
     * @return string
     */
    public function getAddToCycle()
    {
        return $this->addToCycle;
    }

    /**
     * @return string
     */
    public function getCycleDay()
    {
        return $this->cycleDay;
    }

    /**
     * @return string
     */
    public function getUpdateContactEnabled()
    {
        return $this->updateContactEnabled;
    }

    /**
     * @return bool
     */
    public function isSubscriptionEnabled()
    {
        return $this->getSubscriptionEnabled() === self::SUBSCRIPTION_ENABLED;
    }

    /**
     * @return bool
     */
    public function isUpdateContactEnabled()
    {
        return $this->getUpdateContactEnabled() === self::UPDATE_CONTACT_ENABLED;
    }

    /**
     * @return bool
     */
    public function isAddToCycleEnabled()
    {
        return $this->getAddToCycle() === self::ADD_TO_CYCLE_YES;
    }

    /**
     * @return bool
     */
    public function isNewsletterEnabled()
    {
        return $this->getNewsletterEnabled() === self::NEWSLETTER_ENABLED;
    }
}