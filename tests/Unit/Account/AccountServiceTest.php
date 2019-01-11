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

    protected function setUp()
    {
        $this->grAccountService = $this->getMockWithoutConstructing(GrAccountService::class);
        $this->accountSettingsRepository = $this->getMockWithoutConstructing(AccountSettingsRepository::class);

        $this->sut = new AccountService(
            $this->grAccountService,
            $this->accountSettingsRepository
        );
    }

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
}
