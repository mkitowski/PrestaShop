<?php
namespace GetResponse\Contact;

use Customer;
use GetResponse\CustomFields\CustomFieldsServiceFactory;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\Contact\AddContactCommand;
use GrShareCode\Contact\ContactService as GrContactService;
use GrShareCode\CustomField\CustomFieldCollection;
use GrShareCode\GetresponseApiException;
use GetResponse\CustomFieldsMapping\CustomFieldMappingCollection;
use GetResponse\CustomFieldsMapping\CustomFieldMappingServiceFactory;
use PrestaShopDatabaseException;

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
     * @param Customer $contact
     * @param AddContactSettings $addContactSettings
     * @param bool $isNewsletterContact
     * @throws GetresponseApiException
     * @throws ApiTypeException
     * @throws PrestaShopDatabaseException
     */
    public function addContact(Customer $contact, AddContactSettings $addContactSettings, $isNewsletterContact = false)
    {
        if ($addContactSettings->isUpdateContactCustomFields() && !$isNewsletterContact) {

            $customFieldMappingService = CustomFieldMappingServiceFactory::create();
            $customFieldMappingCollection = $customFieldMappingService->getActiveCustomFieldMapping();

            $customFieldsService = CustomFieldsServiceFactory::create();
            $customFieldsService->addCustomsIfMissing($customFieldMappingCollection);

            $grCustomFieldCollection = $customFieldsService->getCustomFieldsFromGetResponse($customFieldMappingCollection);

        } else {
            $customFieldMappingCollection = new CustomFieldMappingCollection();
            $grCustomFieldCollection = new CustomFieldCollection();
        }

        $addContactCommandFactory = new AddContactCommandFactory(
            $customFieldMappingCollection,
            $grCustomFieldCollection
        );

        $addContactCommand = $addContactCommandFactory->createFromContactAndSettings(
            $contact,
            $addContactSettings->getContactListId(),
            $addContactSettings->getDayOfCycle(),
            $addContactSettings->isUpdateContactCustomFields()
        );

        $this->grContactService->upsertContact($addContactCommand);
    }

    /**
     * @param AddContactCommand $addContactCommand
     * @throws GetresponseApiException
     */
    public function upsertContact(AddContactCommand $addContactCommand)
    {
        $this->grContactService->upsertContact($addContactCommand);
    }
}