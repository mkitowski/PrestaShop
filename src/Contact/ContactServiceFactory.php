<?php
namespace GetResponse\Contact;

use Db;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponseRepository;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\Contact\ContactService as GrContactService;
use GrShareCode\GetresponseApiClient;
use GrShop;

/**
 * Class ContactServiceFactory
 * @package GetResponse\Contact
 */
class ContactServiceFactory
{
    /**
     * @return ContactService
     * @throws ApiTypeException
     */
    public static function create()
    {
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId());
        $api = ApiFactory::createFromSettings($accountSettingsRepository->getSettings());
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $apiClient = new GetresponseApiClient($api, $repository);

        return new ContactService(
            new GrContactService($apiClient)
        );
    }
}