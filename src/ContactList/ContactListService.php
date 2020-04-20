<?php
/**
 * 2007-2020 PrestaShop
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
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace GetResponse\ContactList;

use GrApiException;
use GrShareCode\ContactList\Command\AddContactListCommand;
use GrShareCode\ContactList\AutorespondersCollection;
use GrShareCode\ContactList\ContactListCollection;
use GrShareCode\ContactList\ContactListService as GrContactListService;
use GrShareCode\ContactList\FromFieldsCollection;
use GrShareCode\ContactList\SubscriptionConfirmation\SubscriptionConfirmationBodyCollection;
use GrShareCode\ContactList\SubscriptionConfirmation\SubscriptionConfirmationSubjectCollection;
use GrShareCode\Api\Exception\GetresponseApiException;

/**
 * Class ContactListService
 * @package GetResponse\ContactList
 */
class ContactListService
{
    /** @var GrContactListService */
    private $grContactListService;

    /**
     * @param GrContactListService $grContactListService
     */
    public function __construct(GrContactListService $grContactListService)
    {
        $this->grContactListService = $grContactListService;
    }

    /**
     * @return SubscriptionConfirmationSubjectCollection
     * @throws GetresponseApiException
     */
    public function getSubscriptionConfirmationSubject()
    {
        return $this->grContactListService->getSubscriptionConfirmationSubjects();
    }

    /**
     * @return SubscriptionConfirmationBodyCollection
     * @throws GetresponseApiException
     */
    public function getSubscriptionConfirmationBody()
    {
        return $this->grContactListService->getSubscriptionConfirmationsBody();
    }

    /**
     * @return FromFieldsCollection
     * @throws GetresponseApiException
     */
    public function getFromFields()
    {
        return $this->grContactListService->getFromFields();
    }

    /**
     * @return ContactListCollection
     * @throws GetresponseApiException
     */
    public function getContactLists()
    {
        return $this->grContactListService->getAllContactLists();
    }

    /**
     * @return AutorespondersCollection
     * @throws GetresponseApiException
     */
    public function getAutoresponders()
    {
        return $this->grContactListService->getAutoresponders();
    }

    /**
     * @param AddContactListDto $addContactListDto
     * @param string $languageCode
     * @throws GrApiException
     */
    public function createContactList(AddContactListDto $addContactListDto, $languageCode)
    {
        try {
            $this->grContactListService->createContactList(
                new AddContactListCommand(
                    $addContactListDto->getContactListName(),
                    $addContactListDto->getFromField(),
                    $addContactListDto->getReplyTo(),
                    $addContactListDto->getBodyId(),
                    $addContactListDto->getSubjectId(),
                    $languageCode
                )
            );
        } catch (GetresponseApiException $e) {
            throw GrApiException::createForCampaignNotAddedException($e);
        }
    }
}
