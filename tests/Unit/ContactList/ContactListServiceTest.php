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

namespace GetResponse\Tests\Unit\ContactList;

use GetResponse\ContactList\AddContactListDto;
use GetResponse\ContactList\ContactListService;
use GetResponse\Tests\Unit\BaseTestCase;
use GrApiException;
use GrShareCode\ContactList\Command\AddContactListCommand;
use GrShareCode\ContactList\ContactListService as GrContactListService;
use GrShareCode\Api\Exception\GetresponseApiException;
use PHPUnit_Framework_MockObject_MockObject;

class ContactListServiceTest extends BaseTestCase
{

    /** @var ContactListService */
    private $sut;

    /** @var GrContactListService | PHPUnit_Framework_MockObject_MockObject*/
    private $grContactListService;

    protected function setUp()
    {
        $this->grContactListService = $this->getMockWithoutConstructing(GrContactListService::class);

        $this->sut = new ContactListService($this->grContactListService);
    }

    /**
     * @test
     * @throws GrApiException
     */
    public function shouldCreateContactListFromAddContactListCommand()
    {
        $contactListName = 'contactListName';
        $fromField = 'fromField';
        $replyTo = 'replyTo';
        $subjectId = 'subjectId';
        $bodyId = 'bodyId';
        $languageCode = 'languageCode';

        $addContactListDto = new AddContactListDto(
            $contactListName,
            $fromField,
            $replyTo,
            $subjectId,
            $bodyId
        );

        $this->grContactListService
            ->expects(self::once())
            ->method('createContactList')
            ->with(new AddContactListCommand(
                $contactListName,
                $fromField,
                $replyTo,
                $bodyId,
                $subjectId,
                $languageCode
            ))
            ->willReturn(['campaignId' => 'campaignId']);

        $this->sut->createContactList($addContactListDto, $languageCode);
    }

    /**
     * @test
     * @throws GrApiException
     */
    public function shouldCreateContactListFromAddContactListCommandThrowGrApiException()
    {
        $contactListName = 'contactListName';
        $fromField = 'fromField';
        $replyTo = 'replyTo';
        $subjectId = 'subjectId';
        $bodyId = 'bodyId';
        $languageCode = 'languageCode';

        $addContactListDto = new AddContactListDto(
            $contactListName,
            $fromField,
            $replyTo,
            $subjectId,
            $bodyId
        );

        $this->grContactListService
            ->expects(self::once())
            ->method('createContactList')
            ->with(new AddContactListCommand(
                $contactListName,
                $fromField,
                $replyTo,
                $bodyId,
                $subjectId,
                $languageCode
            ))
            ->willThrowException(new GetresponseApiException());

        $this->expectException(GrApiException::class);

        $this->sut->createContactList($addContactListDto, $languageCode);
    }
}
