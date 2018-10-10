<?php
namespace GetResponse\Tests\Unit\ContactList;

use GetResponse\Account\AccountSettings;
use GetResponse\ContactList\ContactListRepository;
use GetResponse\ContactList\ContactListService;
use GetResponse\ContactList\SubscribeViaRegistrationDto;
use GetResponse\Tests\Unit\BaseTestCase;
use GrShareCode\ContactList\AddContactListCommand;
use GrShareCode\ContactList\ContactListService as GrContactListService;
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
     */
    public function shouldUpdateSubscribeViaRegistrationWithEnabledOptions()
    {
        $contactListId = 'contactListId';

        $subscribeViaRegistrationDto = new SubscribeViaRegistrationDto(
            '1',
            '1',
            $contactListId,
            '1',
            '0',
            '1'
        );

        $this->repository
            ->expects(self::once())
            ->method('updateSettings')
            ->with('yes', $contactListId, 'yes', '0', 'yes');

        $this->sut->updateSubscribeViaRegistration($subscribeViaRegistrationDto);
    }

    /**
     * @test
     */
    public function shouldUpdateSubscribeViaRegistrationWithDisabledOptions()
    {
        $contactListId = 'contactListId';

        $subscribeViaRegistrationDto = new SubscribeViaRegistrationDto(
            '0',
            '0',
            $contactListId,
            '0',
            null,
            '0'
        );

        $this->repository
            ->expects(self::once())
            ->method('updateSettings')
            ->with('no', $contactListId, 'no', null, 'no');

        $this->sut->updateSubscribeViaRegistration($subscribeViaRegistrationDto);
    }

    /**
     * @test
     */
    public function shouldCreateContactListFromAddContactListCommand()
    {
        $addContactListCommand = new AddContactListCommand();

        $this->grContactListService
            ->expects(self::once())
            ->method('createContactList')
            ->with($addContactListCommand)
            ->willReturn(['campaignId' => 'campaignId']);

        $this->sut->createContactList($addContactListCommand);
    }
//
//    /**
//     * @test
//     */
//    public function shouldCreateContactListFromAddContactListCommandThrowGrApiException()
//    {
//        $addContactListCommand = new AddContactListCommand();
//
//        $this->grContactListService
//            ->expects(self::once())
//            ->method('createContactList')
//            ->with($addContactListCommand)
//            ->willReturn((new \stdClass())->codeDescription);
//
//        $this->expectException(\GrApiException::class);
//
//        $this->sut->createContactList($addContactListCommand);
//    }

}
