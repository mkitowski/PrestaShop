<?php
namespace GetResponse\Export;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponse\Contact\Contact;
use GetResponse\Contact\ContactCustomFieldCollectionFactory;
use GetResponse\CustomFields\CustomFieldsServiceFactory;
use GetResponse\CustomFieldsMapping\CustomFieldMappingServiceFactory;
use GetResponse\Helper\Shop;
use GetResponse\Order\OrderFactory;
use GetResponse\Product\ProductFactory;
use GrShareCode\Contact\ContactCustomField;
use GrShareCode\Export\ExportContactServiceFactory;
use GrShareCode\GetresponseApiClient;

/**
 * Class ExportServiceFactory
 * @package GetResponse\Export
 */
class ExportServiceFactory
{

    /**
     * @return ExportService
     * @throws \GrShareCode\Api\ApiTypeException
     * @throws \PrestaShopDatabaseException
     */
    public static function create()
    {
        $exportRepository = new ExportRepository(Db::getInstance(), Shop::getUserShopId());

        $getResponseRepository = new \GetResponseRepository(Db::getInstance(), Shop::getUserShopId());

        return new ExportService(
            $exportRepository,
            ExportContactServiceFactory::create(
                new GetresponseApiClient(
                    ApiFactory::createFromSettings(
                        (new AccountSettingsRepository(Db::getInstance(), Shop::getUserShopId()))->getSettings()
                    ),
                    $getResponseRepository
                ),
                $getResponseRepository,
                new ContactCustomField('6bT0x', [Contact::ORIGIN])
            ),
            new OrderFactory(new ProductFactory()),
            CustomFieldMappingServiceFactory::create(),
            CustomFieldsServiceFactory::create(),
            new ContactCustomFieldCollectionFactory()
        );
    }
}