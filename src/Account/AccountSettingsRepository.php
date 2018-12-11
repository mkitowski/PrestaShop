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
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace GetResponse\Account;

use Configuration;
use GetResponse\Ecommerce\EcommerceRepository;
use GetResponse\Settings\Registration\RegistrationServiceFactory;
use GetResponse\WebForm\WebFormRepository;
use GetResponse\WebTracking\WebTrackingRepository;

/**
 * Class AccountSettingsRepository
 * @package GetResponse\Account
 */
class AccountSettingsRepository
{
    const RESOURCE_KEY = 'getresponse_account';

    /**
     * @return AccountSettings
     */
    public function getSettings()
    {
        $result = json_decode(Configuration::get(self::RESOURCE_KEY), true);

        if (empty($result)) {
            return AccountSettings::createEmptyInstance();
        }

        return AccountSettings::createFromSettings($result);
    }

    /**
     * @param string $apiKey
     * @param string $accountType
     * @param string $domain
     */
    public function updateApiSettings($apiKey, $accountType, $domain)
    {
        Configuration::updateValue(self::RESOURCE_KEY, json_encode([
            'api_key' => $apiKey,
            'type' => $accountType,
            'domain' => $domain
        ]));
    }

    public function clearConfiguration()
    {
        $this->clearSettings();
        $registrationService = RegistrationServiceFactory::createService();
        $registrationService->clearSettings();

        (new WebFormRepository())->clearSettings();
        (new WebTrackingRepository())->clearWebTracking();
        (new EcommerceRepository())->clearEcommerceSettings();
        (new AccountRepository())->clearInvalidRequestDate();
        (new AccountRepository())->clearOriginCustomFieldId();
    }

    public function clearSettings()
    {
        Configuration::updateValue(self::RESOURCE_KEY, null);
    }
}
