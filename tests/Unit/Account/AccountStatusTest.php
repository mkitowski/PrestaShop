<?php
namespace GetResponse\Tests\Unit\Account;

use GetResponse\Account\AccountSettings;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Account\AccountStatus;
use GetResponse\Tests\Unit\BaseTestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class AccountStatusTest
 * @package GetResponse\Tests\Unit\Account
 */
class AccountStatusTest extends BaseTestCase
{
    /** @var AccountSettingsRepository | PHPUnit_Framework_MockObject_MockObject */
    private $repository;

    /** @var AccountStatus */
    private $sut;

    protected function setUp()
    {
        $this->repository = $this->getMockWithoutConstructing(AccountSettingsRepository::class);
        $this->sut = new AccountStatus($this->repository);
    }

    /**
     * @test
     */
    public function shouldReturnFalseIfNotConnectedToGetResponse()
    {
        $settings = new AccountSettings('', 'smb', '');

        $this->repository
            ->expects(self::exactly(2))
            ->method('getSettings')
            ->willReturn($settings, null);

        $this->assertFalse($this->sut->isConnectedToGetResponse());
        $this->assertFalse($this->sut->isConnectedToGetResponse());
    }

    /**
     * @test
     */
    public function shouldReturnTrueIfConnectedToGetResponse()
    {
        $settings = new AccountSettings('apiKey', 'smb', '');

        $this->repository
            ->expects(self::once())
            ->method('getSettings')
            ->willReturn($settings);

        $this->assertTrue($this->sut->isConnectedToGetResponse());
    }
}
