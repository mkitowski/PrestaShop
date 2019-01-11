<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author     Getresponse <grintegrations@getresponse.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

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
