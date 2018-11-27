<?php
namespace GetResponse\CustomFields;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponse\Helper\Shop;
use GetResponseRepository;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\CustomField\CustomFieldService as GrCustomFieldService;
use GrShareCode\Api\GetresponseApiClient;

/**
 * Class GrCustomFieldsServiceFactory
 */
class GrCustomFieldsServiceFactory
{
    /**
     * @return GrCustomFieldService
     * @throws ApiTypeException
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository();
        $api = ApiFactory::createFromSettings($accountSettingsRepository->getSettings());
        $repository = new GetResponseRepository(Db::getInstance(), Shop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new GrCustomFieldService($apiClient);
    }
}
