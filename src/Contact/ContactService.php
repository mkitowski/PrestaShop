<?php
namespace GetResponse\Contact;

use GetResponse\Customer\Customer;
use GrShareCode\Contact\ContactService as GrContactService;
use GrShareCode\Api\Exception\GetresponseApiException;
use GetResponse\CustomFieldsMapping\CustomFieldMappingCollection;
use GetResponse\CustomFieldsMapping\CustomFieldMappingServiceFactory;

/**
 * Class ContactService
 * @package GetResponse\Contact
 */
class ContactService
{
    /** @var GrContactService */
    private $grContactService;

    /**
     * @param GrContactService $grContactService
     */
    public function __construct(GrContactService $grContactService)
    {
        $this->grContactService = $grContactService;
    }

    /**
     * @param Customer $customer
     * @param AddContactSettings $addContactSettings
     * @param bool $isNewsletterContact
     * @throws GetresponseApiException
     */
    public function addContact(Customer $customer, AddContactSettings $addContactSettings, $isNewsletterContact = false)
    {
        if ($addContactSettings->isUpdateContactCustomFields() && !$isNewsletterContact) {
            $customFieldMappingService = CustomFieldMappingServiceFactory::create();
            $customFieldMappingCollection = $customFieldMappingService->getActiveCustomFieldMapping();
        } else {
            $customFieldMappingCollection = new CustomFieldMappingCollection();
        }

        $addContactCommandFactory = new AddContactCommandFactory($customFieldMappingCollection);

        $addContactCommand = $addContactCommandFactory->createFromContactAndSettings(
            $customer,
            $addContactSettings->getContactListId(),
            $addContactSettings->getDayOfCycle(),
            $addContactSettings->isUpdateContactCustomFields()
        );

        $this->grContactService->addContact($addContactCommand);
    }
}
