<?php
namespace GetResponse\Contact;

use GetResponse\Customer\Customer;
use GetResponse\CustomFields\CustomFieldsServiceFactory;
use GrShareCode\Contact\ContactService as GrContactService;
use GrShareCode\Api\Exception\GetresponseApiException;
use GetResponse\CustomFieldsMapping\CustomFieldMappingCollection;

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
            $customFieldService = CustomFieldsServiceFactory::create();
            $customFieldMappingCollection = $customFieldService->getActiveCustomFieldMapping();
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
