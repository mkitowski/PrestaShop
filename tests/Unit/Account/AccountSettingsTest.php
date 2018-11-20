<?php
namespace GetResponse\Tests\Unit\Account;

use GetResponse\Account\AccountSettings;
use GetResponse\Tests\Unit\BaseTestCase;

/**
 * Class AccountSettingsTest
 * @package GetResponse\Tests\Unit\Account
 */
class AccountSettingsTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldReturnHiddenApiKey()
    {
        $settings = new AccountSettings(
            'QQQQQQQQQQQQ',
             'accountType',
            'domain'
        );

        $this->assertEquals('******QQQQQQ', $settings->getHiddenApiKey());
    }
}
