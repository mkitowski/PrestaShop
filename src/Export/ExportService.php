<?php
namespace GetResponse\Export;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponse\Contact\AddContactCommandFactory;
use GetResponse\Contact\Contact;
use GetResponse\Contact\ContactCustomFieldCollectionFactory;
use GetResponse\Contact\ContactService;
use GetResponse\Contact\ContactServiceFactory;
use GetResponse\CustomFields\CustomFieldCollectionFactory;
use GetResponse\CustomFields\CustomFieldsServiceFactory;
use GetResponse\CustomFieldsMapping\CustomFieldMappingServiceFactory;
use GetResponse\Helper\Shop as GrShop;
use GetResponseRepository;
use GrShareCode\Cart\CartService as GrCartService;
use GrShareCode\Contact\AddContactCommand;
use GrShareCode\Contact\ContactService as GrContactService;
use GrShareCode\GetresponseApiClient;
use GrShareCode\GetresponseApiException;
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


    /**
     * @param ExportSettings $exportSettings
     * @throws \GrShareCode\Api\ApiTypeException
     * @throws \GrShareCode\GetresponseApiException
     */
    public function export(ExportSettings $exportSettings)
    {

//        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId());
//        $api = ApiFactory::createFromSettings($accountSettingsRepository->getSettings());
//        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
//        $apiClient = new GetresponseApiClient($api, $repository);


//        $contactService = new GrContactService($apiClient);
//        $productService = new GrProductService($apiClient, $repository);
//        $cartService = new GrCartService($apiClient, $repository, $productService);
//        $orderService = new GrOrderService($apiClient, $repository, $productService);


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

            $addContactCommand = $addContactCommandFactory->createFromContactAndSettings(
                (object) $contact,
                $exportSettings->getContactListId(),
                $exportSettings->getCycleDay(),
                $exportSettings->isUpdateContactInfo()
            );

            try {
                $contactService->addContact($addContactCommand);
            } catch (GetresponseApiException $e) {
                if ($e->getMessage() !== 'Cannot add contact that is blacklisted' && $e->getMessage() !== 'Contact in queue') {
                    throw $e;
                }
            }
        }

    }
}