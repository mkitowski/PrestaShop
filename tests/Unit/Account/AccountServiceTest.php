<?php
namespace GetResponse\Tests\Unit\Account;

use GetResponse\Account\AccountService;
use GetResponse\Account\AccountSettings;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Tests\Unit\BaseTestCase;
use GrShareCode\Account\AccountService as GrAccountService;
use GrShareCode\TrackingCode\TrackingCode;
use GrShareCode\TrackingCode\TrackingCodeService;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class AccountServiceTest
 * @package GetResponse\Tests\Unit\Account
 */
class AccountServiceTest extends BaseTestCase
{

    /** @var AccountService */
    private $sut;

    /** @var TrackingCodeService | PHPUnit_Framework_MockObject_MockObject */
    private $trackingCodeService;

    /** @var AccountSettingsRepository | PHPUnit_Framework_MockObject_MockObject */
    private $accountSettingsRepository;

    /** @var GrAccountService | PHPUnit_Framework_MockObject_MockObject */
    private $grAccountService;

    /**
     * @test
     */
    public function shouldUpdateApiSettingsWithInactiveTrackingCode()
    {
        $apiKey = 'apiKey';
        $accountType = 'accountType';
        $domain = 'domain';
        $trackingCodeSnippet = 'trackingCodeSnippet';

        $trackingCode = new TrackingCode(true, $trackingCodeSnippet);

        $this->trackingCodeService
            ->expects(self::once())
            ->method('getTrackingCode')
            ->willReturn($trackingCode);

        $this->accountSettingsRepository
            ->expects(self::once())
            ->method('updateTracking')
            ->with(
                'no',
                $trackingCodeSnippet
            );

        $this->accountSettingsRepository
            ->expects(self::once())
            ->method('updateApiSettings')
            ->with($apiKey, $accountType, $domain);

        $this->sut->updateApiSettings($apiKey, $accountType, $domain);
    }

    /**
     * @test
     */
    public function shouldUpdateApiSettingsWithDisabledTrackingCode()
    {
        $apiKey = 'apiKey';
        $accountType = 'accountType';
        $domain = 'domain';
        $trackingCodeSnippet = 'trackingCodeSnippet';

        $trackingCode = new TrackingCode(false, $trackingCodeSnippet);

        $this->trackingCodeService
            ->expects(self::once())
            ->method('getTrackingCode')
            ->willReturn($trackingCode);

        $this->accountSettingsRepository
            ->expects(self::once())
            ->method('updateTracking')
            ->with(
                'disabled',
                $trackingCodeSnippet
            );

        $this->accountSettingsRepository
            ->expects(self::once())
            ->method('updateApiSettings')
            ->with($apiKey, $accountType, $domain);

        $this->sut->updateApiSettings($apiKey, $accountType, $domain);
    }

    /**
     * @test
     */
    public function shouldDisconnectPluginFromGetResponse()
    {
        $this->accountSettingsRepository
            ->expects(self::once())
            ->method('disconnectApiSettings');

        $this->sut->disconnectFromGetResponse();
    }

    /**
     * @test
     */
    public function shouldReturnTrueWhenPluginIsConnectToGetResponse()
    {
        $apiKey = 'api_key';

        $settings = new AccountSettings(
            'id',
            'shopId',
            $apiKey,
            'yes',
            'yes',
            'yes',
            'trackingCodeSnippet',
            'yes',
            'contactListId',
            '3',
            'smb',
            ''
        );

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

        $settings = new AccountSettings(
            'id',
            'shopId',
            $apiKey,
            'yes',
            'yes',
            'yes',
            'trackingCodeSnippet',
            'yes',
            'contactListId',
            '3',
            'smb',
            ''
        );

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
        $this->trackingCodeService = $this->getMockWithoutConstructing(TrackingCodeService::class);

        $this->sut = new AccountService(
            $this->grAccountService,
            $this->accountSettingsRepository,
            $this->trackingCodeService
        );
    }
}
