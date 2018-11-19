<?php
namespace GetResponse\Tests\Unit\ContactList;

use GetResponse\Account\AccountSettings;
use GetResponse\ContactList\AddContactListDto;
use GetResponse\ContactList\ContactListRepository;
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

    /** @var ContactListRepository | PHPUnit_Framework_MockObject_MockObject */
    private $repository;

    /** @var GrContactListService | PHPUnit_Framework_MockObject_MockObject*/
    private $grContactListService;

    /** @var AccountSettings | PHPUnit_Framework_MockObject_MockObject */
    private $accountSettings;

    protected function setUp()
    {
        $this->repository = $this->getMockWithoutConstructing(ContactListRepository::class);
        $this->grContactListService = $this->getMockWithoutConstructing(GrContactListService::class);
        $this->accountSettings = $this->getMockWithoutConstructing(AccountSettings::class);

        $this->sut = new ContactListService(
            $this->repository,
            $this->grContactListService,
            $this->accountSettings
        );
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
