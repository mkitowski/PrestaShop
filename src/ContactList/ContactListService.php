<?php
namespace GetResponse\ContactList;

use Exception;
use GetResponse\Account\AccountSettings;
use GrApiException;
use GrShareCode\ContactList\AddContactListCommand;
use GrShareCode\ContactList\AutorespondersCollection;
use GrShareCode\ContactList\ContactListCollection;
use GrShareCode\ContactList\ContactListService as GrContactListService;
use GrShareCode\ContactList\FromFieldsCollection;
use GrShareCode\ContactList\SubscriptionConfirmation\SubscriptionConfirmationBodyCollection;
use GrShareCode\ContactList\SubscriptionConfirmation\SubscriptionConfirmationSubjectCollection;
use GrShareCode\GetresponseApiException;

/**
 * Class ContactListService
 * @package GetResponse\ContactList
 */
class ContactListService
{
    /** @var GrContactListService */
    private $grContactListService;

    /** @var AccountSettings */
    private $settings;

    /** @var ContactListRepository */
    private $repository;

    /**
     * @param ContactListRepository $repository
     * @param GrContactListService $grContactListService
     * @param AccountSettings $settings
     */
    public function __construct(
        ContactListRepository $repository,
        GrContactListService $grContactListService,
        AccountSettings $settings
    ) {
        $this->grContactListService = $grContactListService;
        $this->settings = $settings;
        $this->repository = $repository;
    }

    /**
     * @return SubscriptionConfirmationSubjectCollection
     * @throws GetresponseApiException
     */
    public function getSubscriptionConfirmationSubject()
    {
        return $this->grContactListService->getSubscriptionConfirmationSubjects();
    }

    /**
     * @return SubscriptionConfirmationBodyCollection
     * @throws GetresponseApiException
     */
    public function getSubscriptionConfirmationBody()
    {
        return $this->grContactListService->getSubscriptionConfirmationsBody();
    }

    /**
     * @return FromFieldsCollection
     * @throws GetresponseApiException
     */
    public function getFromFields()
    {
        return $this->grContactListService->getFromFields();
    }

    /**
     * @return ContactListCollection
     * @throws GetresponseApiException
     */
    public function getContactLists()
    {
        return $this->grContactListService->getAllContactLists();
    }

    /**
     * @return AutorespondersCollection
     * @throws GetresponseApiException
     */
    public function getAutoresponders()
    {
        return $this->grContactListService->getAutoresponders();
    }

    /**
     * @return AccountSettings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param SubscribeViaRegistrationDto $subscribeViaRegistrationDto
     */
    public function updateSubscribeViaRegistration(SubscribeViaRegistrationDto $subscribeViaRegistrationDto)
    {
        $subscription = $subscribeViaRegistrationDto->isSubscriptionEnabled()
            ? AccountSettings::SUBSCRIPTION_ACTIVE_YES
            : AccountSettings::SUBSCRIPTION_ACTIVE_NO;

        $updateContact = $subscribeViaRegistrationDto->isUpdateContactEnabled()
            ? AccountSettings::UPDATE_ADDRESS_YES
            : AccountSettings::UPDATE_ADDRESS_NO;

        $cycleDay = $subscribeViaRegistrationDto->isAddToCycleEnabled()
            ? $subscribeViaRegistrationDto->getCycleDay()
            : null;

        $newsletterSubscribers = $subscribeViaRegistrationDto->isNewsletterEnabled()
            ? AccountSettings::NEWSLETTER_SUBSCRIPTION_ACTIVE_YES
            : AccountSettings::NEWSLETTER_SUBSCRIPTION_ACTIVE_NO;

        $this->repository->updateSettings(
            $subscription,
            $subscribeViaRegistrationDto->getContactList(),
            $updateContact,
            $cycleDay,
            $newsletterSubscribers
        );
    }

    /**
     * @param AddContactListDto $addContactListDto
     * @param string $languageCode
     * @throws GrApiException
     */
    public function createContactList(AddContactListDto $addContactListDto, $languageCode)
    {
        try {
            $this->grContactListService->createContactList(
                new AddContactListCommand(
                    $addContactListDto->getContactListName(),
                    $addContactListDto->getFromField(),
                    $addContactListDto->getReplyTo(),
                    $addContactListDto->getBodyId(),
                    $addContactListDto->getSubjectId(),
                    $languageCode
                )
            );
        } catch (GetresponseApiException $e) {
            throw GrApiException::createForCampaignNotAddedException($e);
        }
    }

}