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
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace GetResponse\Contact;

use GetResponse\Customer\Customer;
use GetResponse\CustomFieldsMapping\CustomFieldMappingCollection;
use GrShareCode\Contact\ContactService as GrContactService;
use GrShareCode\Api\Exception\GetresponseApiException;

/**
 * Class ContactService
 * @package GetResponse\Contact
 */
class ContactService
{
    /** @var GrContactService */
    private $grContactService;

    /**
     * @param GrContactService $grContactService
     */
    public function __construct(GrContactService $grContactService)
    {
        $this->grContactService = $grContactService;
    }

    /**
     * @param Customer $customer
     * @param AddContactSettings $addContactSettings
     * @param bool $withCustomMapping
     * @throws GetresponseApiException
     */
    public function addContact(Customer $customer, AddContactSettings $addContactSettings, $withCustomMapping = true)
    {
        if ($withCustomMapping) {
            $customFieldMappingCollection = $addContactSettings->getCustomFieldMappingCollection();
        } else {
            $customFieldMappingCollection = new CustomFieldMappingCollection();
        }

        $addContactCommandFactory = new AddContactCommandFactory($customFieldMappingCollection);

        $addContactCommand = $addContactCommandFactory->createFromContactAndSettings(
            $customer,
            $addContactSettings->getContactListId(),
            $addContactSettings->getDayOfCycle()
        );

        $this->grContactService->addContact($addContactCommand);
    }
}
