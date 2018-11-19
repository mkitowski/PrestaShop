<?php
namespace GetResponse\Export;

use Customer;
use GetResponse\Contact\ContactCustomFieldCollectionFactory;
use GetResponse\CustomFields\CustomFieldService;
use GetResponse\CustomFieldsMapping\CustomFieldsMappingService;
use GetResponse\Order\OrderFactory;
use GrShareCode\Contact\ContactCustomField\ContactCustomFieldsCollection;
use GrShareCode\Export\Command\ExportContactCommand;
use GrShareCode\Export\ExportContactService;
use GrShareCode\Export\Settings\EcommerceSettings as ShareCodeEcommerceSettings;
use GrShareCode\Export\Settings\ExportSettings as ShareCodeExportSettings;
use GrShareCode\Api\Exception\GetresponseApiException;
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
     */
    public function export(ExportSettings $exportSettings)
    {
        $contacts = $this->exportRepository->getContacts($exportSettings->isNewsletterSubsIncluded());

        if (!count($contacts)) {
            return;
        }

        if ($exportSettings->isUpdateContactInfo()) {
            try {
                $customFieldMappingCollection = $this->customFieldsMappingService->getActiveCustomFieldMapping();
                $this->customFieldsService->addCustomsIfMissing($customFieldMappingCollection);
                $grCustomFieldCollection = $this->customFieldsService->getCustomFieldsFromGetResponse($customFieldMappingCollection);
            } catch (GetresponseApiException $e) {
                // @todo log
                return;
            }
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
                $customer = new Customer();
                $customer->email = $contact['email'];
            } else {
                $customer = new Customer($contact['id']);

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
                        $grCustomFieldCollection,
                        $exportSettings->isUpdateContactInfo()
                    );
            } else {
                $contactCustomFieldCollection = new ContactCustomFieldsCollection();
            }

            try {
                $this->shareCodeExportContactService->exportContact(
                    new ExportContactCommand(
                        $customer->email,
                        $customer->firstname . ' ' . $customer->lastname,
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