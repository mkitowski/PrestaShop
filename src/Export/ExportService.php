<?php
namespace GetResponse\Export;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponse\Contact\Contact;
use GetResponse\Contact\ContactCustomFieldCollectionFactory;
use GetResponse\CustomFields\CustomFieldCollectionFactory;
use GetResponse\CustomFields\CustomFieldsServiceFactory;
use GetResponse\CustomFieldsMapping\CustomFieldMappingServiceFactory;
use GetResponse\Helper\Shop as GrShop;
use GetResponseRepository;
use GrShareCode\Cart\CartService as GrCartService;
use GrShareCode\Contact\AddContactCommand;
use GrShareCode\Contact\ContactService as GrContactService;
use GrShareCode\GetresponseApiClient;
use GrShareCode\Order\OrderService as GrOrderService;
use GrShareCode\Product\ProductService as GrProductService;

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


    public function export(ExportSettings $exportSettings)
    {

        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId());
        $api = ApiFactory::createFromSettings($accountSettingsRepository->getSettings());
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);


        $contactService = new GrContactService($apiClient);
        $productService = new GrProductService($apiClient, $repository);
        $cartService = new GrCartService($apiClient, $repository, $productService);
        $orderService = new GrOrderService($apiClient, $repository, $productService);


        $contacts = $this->exportRepository->getContacts($exportSettings->isNewsletterSubsIncluded());

        if (!count($contacts)) {
            return;
        }

        $customFieldMappingService = CustomFieldMappingServiceFactory::create();
        $customFieldMappingCollection = $customFieldMappingService->getAllCustomFieldMapping();

        $customFieldsService = CustomFieldsServiceFactory::create();
        $customFieldsService->addCustomsIfMissing($customFieldMappingCollection);
        $grCustomFieldCollection = $customFieldsService->getAllCustomFields();

        foreach ($contacts as $contact) {

            $customFields = ContactCustomFieldCollectionFactory::createFromContactAndCustomFieldMapping(
                $contact,
                $customFieldMappingCollection,
                $grCustomFieldCollection,
                $exportSettings->isUpdateContactInfo()
            );

            $addContactCommand = new AddContactCommand(
                $contact['email'],
                $contact['firstname'] . ' ' . $contact['lastname'],
                $exportSettings->getContactListId(),
                $exportSettings->getCycleDay(),
                $customFields,
                Contact::ORIGIN
            );

            $contactService->upsertContact($addContactCommand);

        }

    }
}