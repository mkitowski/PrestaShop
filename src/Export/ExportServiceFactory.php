<?php
namespace GetResponse\Export;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponse\Contact\Contact;
use GetResponse\Contact\ContactCustomFieldCollectionFactory;
use GetResponse\CustomFields\CustomFieldsServiceFactory;
use GetResponse\Helper\Shop;
use GetResponse\Order\OrderFactory;
use GetResponse\Product\ProductFactory;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\Export\ExportContactServiceFactory;
use GrShareCode\Api\GetresponseApiClient;

/**
 * Class ExportServiceFactory
 * @package GetResponse\Export
 */
class ExportServiceFactory
{

    /**
     * @return ExportService
     * @throws \PrestaShopDatabaseException
     * @throws ApiTypeException
     */
    public static function create()
    {
        $exportRepository = new ExportRepository(Db::getInstance(), Shop::getUserShopId());

        $getResponseRepository = new \GetResponseRepository(Db::getInstance(), Shop::getUserShopId());

        $getresponseApiClient = new GetresponseApiClient(
            ApiFactory::createFromSettings(
                (new AccountSettingsRepository())->getSettings()
            ),
            $getResponseRepository
        );

        return new ExportService(
            $exportRepository,
            (new ExportContactServiceFactory())->create(
                $getresponseApiClient,
                $getResponseRepository,
                Contact::ORIGIN
            ),
            new OrderFactory(new ProductFactory()),
            CustomFieldsServiceFactory::create(),
            new ContactCustomFieldCollectionFactory()
        );
    }
}
