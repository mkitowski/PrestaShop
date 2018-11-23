<?php
namespace GetResponse\Export;

use GetResponse\Contact\ContactCustomFieldCollectionFactory;
use GetResponse\Customer\CustomerFactory;
use GetResponse\CustomFields\CustomFieldService;
use GetResponse\CustomFieldsMapping\CustomFieldsMappingService;
use GetResponse\Order\OrderFactory;
use GrShareCode\Contact\ContactCustomField\ContactCustomFieldsCollection;
use GrShareCode\Export\Command\ExportContactCommand;
use GrShareCode\Export\ExportContactService;
use GrShareCode\Export\Settings\EcommerceSettings as ShareCodeEcommerceSettings;
use GrShareCode\Export\Settings\ExportSettings as ShareCodeExportSettings;
use GrShareCode\GrShareCodeException;
use GrShareCode\Order\OrderCollection;
use Order;

/**
 * Class ExportService
 * @package GetResponse\Export
 */
class ExportService
{
    /** @var ExportRepository */
    private $exportRepository;
    /** @var ExportContactService */
    private $shareCodeExportContactService;
    /** @var OrderFactory */
    private $orderFactory;
    /** @var CustomFieldService */
    private $customFieldsService;
    /** @var CustomFieldsMappingService */
    private $customFieldsMappingService;
    /** @var ContactCustomFieldCollectionFactory */
    private $contactCustomFieldCollectionFactory;

    /**
     * @param ExportRepository $exportRepository
     * @param ExportContactService $shareCodeExportContactService
     * @param OrderFactory $orderFactory
     * @param CustomFieldsMappingService $customFieldsMappingService
     * @param CustomFieldService $customFieldsService
     * @param ContactCustomFieldCollectionFactory $contactCustomFieldCollectionFactory
     */
    public function __construct(
        ExportRepository $exportRepository,
        ExportContactService $shareCodeExportContactService,
        OrderFactory $orderFactory,
        CustomFieldsMappingService $customFieldsMappingService,
        CustomFieldService $customFieldsService,
        ContactCustomFieldCollectionFactory $contactCustomFieldCollectionFactory

    ) {
        $this->exportRepository = $exportRepository;
        $this->shareCodeExportContactService = $shareCodeExportContactService;
        $this->orderFactory = $orderFactory;
        $this->customFieldsMappingService = $customFieldsMappingService;
        $this->customFieldsService = $customFieldsService;
        $this->contactCustomFieldCollectionFactory = $contactCustomFieldCollectionFactory;
    }

    /**
     * @param ExportSettings $exportSettings
     * @throws \PrestaShopDatabaseException
     */
    public function export(ExportSettings $exportSettings)
    {
        $contacts = $this->exportRepository->getContacts($exportSettings->isNewsletterSubsIncluded());

        if (!count($contacts)) {
            return;
        }

        if ($exportSettings->isUpdateContactInfo()) {
                $customFieldMappingCollection = $this->customFieldsMappingService->getActiveCustomFieldMapping();
        }

        $shareCodeExportSettings = new ShareCodeExportSettings(
            $exportSettings->getContactListId(),
            $exportSettings->getCycleDay(),
            new ShareCodeEcommerceSettings(
                $exportSettings->isEcommerce(),
                $exportSettings->getShopId()
            )
        );

        foreach ($contacts as $contact) {

            $shareCodeOrderCollection = new OrderCollection();

            if (0 == $contact['id']) {
                // flow for newsletters subscribers
                $customer = CustomerFactory::createFromNewsletter($contact['email']);
            } else {
                $customer = CustomerFactory::createFromArray($contact);

                $customerOrders = $this->exportRepository->getOrders($contact['id']);

                foreach ($customerOrders as $customerOrder) {
                    $shareCodeOrderCollection->add(
                        $this->orderFactory->createShareCodeOrderFromOrder(new Order($customerOrder['id_order']))
                    );
                }
            }

            if ($exportSettings->isUpdateContactInfo()) {
                $contactCustomFieldCollection = $this->contactCustomFieldCollectionFactory
                    ->createFromContactAndCustomFieldMapping(
                        $customer,
                        $customFieldMappingCollection,
                        $exportSettings->isUpdateContactInfo()
                    );
            } else {
                $contactCustomFieldCollection = new ContactCustomFieldsCollection();
            }

            try {
                $this->shareCodeExportContactService->exportContact(
                    new ExportContactCommand(
                        $customer->getEmail(),
                        $customer->getName(),
                        $shareCodeExportSettings,
                        $contactCustomFieldCollection,
                        $shareCodeOrderCollection
                    )
                );
            } catch (GrShareCodeException $e) {
                \PrestaShopLoggerCore::addLog('Getresponse export error: ' . $e->getMessage(), 2, null, 'GetResponse', 'GetResponse');
            }
        }

    }
}
