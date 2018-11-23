<?php
namespace GetResponse\Contact;

use Db;
use GetResponse\Account\AccountServiceFactory;
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
        return new ContactService(
            (new ShareCodeContactServiceFactory())->create(
                new GetresponseApiClient(
                    ApiFactory::createFromSettings(AccountServiceFactory::create()->getAccountSettings()),
                    new GetResponseRepository(Db::getInstance(), Shop::getUserShopId())
                ),
                new GetResponseRepository(Db::getInstance(), Shop::getUserShopId()),
                Contact::ORIGIN
            )
        );
    }
}
