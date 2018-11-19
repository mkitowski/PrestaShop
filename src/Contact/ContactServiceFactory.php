<?php
namespace GetResponse\Contact;

use Db;
use GetResponse\Account\AccountServiceFactory;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GetResponse\Helper\Shop;
use GetResponseRepository;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\Api\Exception\GetresponseApiException;
use GrShareCode\Contact\ContactServiceFactory as ShareCodeContactServiceFactory;
use GrShareCode\Api\GetresponseApiClient;

/**
 * Class ContactServiceFactory
 * @package GetResponse\Contact
 */
class ContactServiceFactory
{
    /**
     * @return ContactService
     * @throws ApiTypeException
     * @throws GetresponseApiException
     */
    public static function createFromSettings()
    {
        return self::inject(
            new GetresponseApiClient(
                ApiFactory::createFromSettings(AccountServiceFactory::create()->getAccountSettings()),
                new GetResponseRepository(Db::getInstance(), Shop::getUserShopId())
            )
        );
    }

    /**
     * @return ContactService
     * @throws ApiTypeException
     * @throws \PrestaShopDatabaseException
     */
    public static function create()
    {
        return self::inject(
            new GetresponseApiClient(
                ApiFactory::createFromSettings(
                    (new AccountSettingsRepository())->getSettings()
                ),
                new GetResponseRepository(Db::getInstance(), Shop::getUserShopId())
            )
        );
    }

    private static function inject(GetresponseApiClient $getresponseApiClient)
    {
        return new ContactService(
            (new ShareCodeContactServiceFactory())->create(
                $getresponseApiClient,
                new GetResponseRepository(Db::getInstance(), Shop::getUserShopId()),
                Contact::ORIGIN
            )
        );
    }
}
