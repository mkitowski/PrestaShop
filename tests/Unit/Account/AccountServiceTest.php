<?php
namespace GetResponse\Tests\Unit\Account;

use GetResponse\Account\AccountService;
use GetResponse\Account\AccountSettings;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Tests\Unit\BaseTestCase;
use GrShareCode\Account\AccountService as GrAccountService;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class AccountServiceTest
 * @package GetResponse\Tests\Unit\Account
 */
class AccountServiceTest extends BaseTestCase
{
    /** @var AccountService */
    private $sut;

    /** @var AccountSettingsRepository | PHPUnit_Framework_MockObject_MockObject */
    private $accountSettingsRepository;

    /** @var GrAccountService | PHPUnit_Framework_MockObject_MockObject */
    private $grAccountService;

    /**
     * @test
     */
    public function shouldDisconnectPluginFromGetResponse()
    {
        $this->accountSettingsRepository
            ->expects(self::once())
            ->method('clearConfiguration');

        $this->sut->disconnectFromGetResponse();
    }

    /**
     * @test
     */
    public function shouldReturnTrueWhenPluginIsConnectToGetResponse()
    {
        $apiKey = 'api_key';
        $settings = new AccountSettings($apiKey, 'smb', '');

        $this->accountSettingsRepository
            ->expects(self::once())
            ->method('getSettings')
            ->willReturn($settings);

        $this->assertTrue($this->sut->isConnectedToGetResponse());
    }

    /**
     * @test
     */
    public function shouldReturnFalseWhenPluginIsNotConnectToGetResponse()
    {
        $apiKey = '';
        $settings = new AccountSettings($apiKey, 'smb', '');

        $this->accountSettingsRepository
            ->expects(self::once())
            ->method('getSettings')
            ->willReturn($settings);

        $this->assertFalse($this->sut->isConnectedToGetResponse());
    }

    protected function setUp()
    {
        $this->grAccountService = $this->getMockWithoutConstructing(GrAccountService::class);
        $this->accountSettingsRepository = $this->getMockWithoutConstructing(AccountSettingsRepository::class);

        $this->sut = new AccountService(
            $this->grAccountService,
            $this->accountSettingsRepository
        );
    }
}
