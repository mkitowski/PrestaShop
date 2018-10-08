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
    public function shouldCheckIfSubscriberCanBeSend()
    {
        $settings = $this->getSettingsAppendedByParams(
            [
                'activeSubscription' => 'yes',
                'contactListId' => 'contactListId',
                'activeNewsletterSubscription' => 'yes',
            ]
        );
        $this->assertTrue($settings->canSubscriberBeSend());

        $settings = $this->getSettingsAppendedByParams(
            [
                'activeSubscription' => 'no',
                'contactListId' => 'contactListId',
                'activeNewsletterSubscription' => 'yes',
            ]
        );
        $this->assertFalse($settings->canSubscriberBeSend());

        $settings = $this->getSettingsAppendedByParams(
            [
                'activeSubscription' => 'yes',
                'contactListId' => '',
                'activeNewsletterSubscription' => 'yes',
            ]
        );
        $this->assertFalse($settings->canSubscriberBeSend());

        $settings = $this->getSettingsAppendedByParams(
            [
                'activeSubscription' => 'yes',
                'contactListId' => 'contactListId',
                'activeNewsletterSubscription' => 'no',
            ]
        );
        $this->assertTrue($settings->canSubscriberBeSend());
    }

    /**
     * @param array $params
     * @return AccountSettings
     */
    private function getSettingsAppendedByParams($params)
    {
        return new AccountSettings(
            isset($params['id']) ? $params['id'] : 'id',
            isset($params['shopId']) ? $params['shopId'] : 'shopId',
            isset($params['apiKey']) ? $params['apiKey'] : 'apiKey',
            isset($params['activeSubscription']) ? $params['activeSubscription'] : 'no',
            isset($params['activeNewsletterSubscription']) ? $params['activeNewsletterSubscription'] : 'no',
            isset($params['activeTracking']) ? $params['activeTracking'] : 'disabled',
            isset($params['trackingSnippet']) ? $params['trackingSnippet'] : 'trackingSnippet',
            isset($params['updateAddress']) ? $params['updateAddress'] : 'updateAddress',
            isset($params['contactListId']) ? $params['contactListId'] : 'contactListId',
            isset($params['cycleDay']) ? $params['cycleDay'] : 'cycleDay',
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

    /**
     * @test
     */
    public function shouldReturnProperTrackingStatusInfo()
    {
        $settings = $this->getSettingsAppendedByParams(['activeTracking' => 'disabled']);
        $this->assertTrue($settings->isTrackingDisabled());

        $settings = $this->getSettingsAppendedByParams(['activeTracking' => 'yes']);
        $this->assertTrue($settings->isTrackingActive());
    }

    /**
     * @test
     */
    public function shouldReturnIfUpdateContactEnabled()
    {
        $settings = $this->getSettingsAppendedByParams(['updateAddress' => 'no']);
        $this->assertFalse($settings->isUpdateContactEnabled());

        $settings = $this->getSettingsAppendedByParams(['updateAddress' => 'yes']);
        $this->assertTrue($settings->isUpdateContactEnabled());
    }

}
