<?php
namespace GetResponse\Export;

use Customer;
use GetResponse\Contact\AddContactCommandFactory;
use GetResponse\Contact\ContactServiceFactory;
use GetResponse\CustomFields\CustomFieldsServiceFactory;
use GetResponse\CustomFieldsMapping\CustomFieldMappingServiceFactory;
use GetResponse\Order\OrderServiceFactory;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\GetresponseApiException;
use Order;
use PrestaShopDatabaseException;

/**
 * Class ExportService
 * @package GetResponse\Export
 */
class ExportService
{
    /**
     * @var ExportRepository
     */
    private $exportRepository;

    public function __construct(ExportRepository $exportRepository)
    {
        $this->exportRepository = $exportRepository;
    }

    /**
     * @param ExportSettings $exportSettings
     * @throws ApiTypeException
     * @throws GetresponseApiException
     * @throws PrestaShopDatabaseException
     */
    public function export(ExportSettings $exportSettings)
    {
        $contacts = $this->exportRepository->getContacts($exportSettings->isNewsletterSubsIncluded());

        if (!count($contacts)) {
            return;
        }

        $customFieldMappingService = CustomFieldMappingServiceFactory::create();
        $customFieldMappingCollection = $customFieldMappingService->getActiveCustomFieldMapping();

        $customFieldsService = CustomFieldsServiceFactory::create();
        $customFieldsService->addCustomsIfMissing($customFieldMappingCollection);
        $grCustomFieldCollection = $customFieldsService->getCustomFieldsFromGetResponse($customFieldMappingCollection);

        $addContactCommandFactory = new AddContactCommandFactory(
            $customFieldMappingCollection,
            $grCustomFieldCollection
        );

        $contactService = ContactServiceFactory::create();

        foreach ($contacts as $contact) {

            if (0 == $contact['id']) {
                // flow for newsletters subscribers
                $customer = new Customer();
                $customer->email = $contact['email'];
            } else {
                $customer = new Customer($contact['id']);
            }

            $addContactCommand = $addContactCommandFactory->createFromContactAndSettings(
                $customer,
                $exportSettings->getContactListId(),
                $exportSettings->getCycleDay(),
                $exportSettings->isUpdateContactInfo()
            );

            try {
                $contactService->upsertContact($addContactCommand);
            } catch (GetresponseApiException $e) {
                // Muted API errors, ex.:
                // - Cannot add contact that is blacklisted
                // - Contact in queue
                // - Email domain not exists
            }
        }

        $this->exportContactOrders($contacts, $exportSettings);

    }

    /**
     * @param array $contacts
     * @param ExportSettings $exportSettings
     * @throws GetresponseApiException
     * @throws ApiTypeException
     * @throws PrestaShopDatabaseException
     */
    private function exportContactOrders($contacts, ExportSettings $exportSettings)
    {

        if (!$exportSettings->isEcommerce()) {
            return;
        }

        $orderService = OrderServiceFactory::create();

        foreach ($contacts as $contact) {

            $customerOrders = $this->exportRepository->getOrders($contact['id']);

            foreach ($customerOrders as $customerOrder) {

                $prestashopOrder = new Order($customerOrder['id_order']);

                $orderService->sendOrder(
                    $prestashopOrder,
                    $exportSettings->getContactListId(),
                    $exportSettings->getShopId(),
                    true
                );
            }
        }
    }
}