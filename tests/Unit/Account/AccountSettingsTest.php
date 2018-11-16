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
     * @param array $params
     * @return AccountSettings
     */
    private function getSettingsAppendedByParams($params)
    {
        return new AccountSettings(
            isset($params['apiKey']) ? $params['apiKey'] : 'apiKey',
            isset($params['accountType']) ? $params['accountType'] : 'accountType',
            isset($params['domain']) ? $params['domain'] : 'domain'
        );
    }

    /**
     * @test
     */
    public function shouldReturnHiddenApiKey()
    {
        $settings = $this->getSettingsAppendedByParams(['apiKey' => 'QQQQQQQQQQQQ']);

        $this->assertEquals('******QQQQQQ', $settings->getHiddenApiKey());
    }
}
