<?php
/**
 * 2007-2020 PrestaShop
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
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace GetResponse\Settings\Registration;

use Configuration;

/**
 * Class RegistrationSettings
 * @package GetResponse\WebTracking
 */
class RegistrationRepository
{
    const RESOURCE_KEY = 'getresponse_registration';
    const MAPPING_KEY = 'getresponse_customs';

    /**
     * @return RegistrationSettings
     */
    public function getSettings()
    {
        $configuration = json_decode(Configuration::get(self::RESOURCE_KEY), true);
        $customs = json_decode(Configuration::get(self::MAPPING_KEY), true);

        if (empty($configuration)) {
            return RegistrationSettings::createEmptyInstance();
        }

        return RegistrationSettings::createFromConfiguration($configuration, $customs);
    }

    /**
     * @param RegistrationSettings $settings
     */
    public function updateSettings(RegistrationSettings $settings)
    {
        $customFieldMappingCollection = $settings->getCustomFieldMappingCollection();

        Configuration::updateValue(
            self::RESOURCE_KEY,
            json_encode([
                'active_subscription' => $settings->isActive(),
                'active_newsletter_subscription' => $settings->isNewsletterActive(),
                'campaign_id' => $settings->getListId(),
                'cycle_day' => $settings->getCycleDay()
            ])
        );

        Configuration::updateValue(
            self::MAPPING_KEY,
            json_encode($customFieldMappingCollection->toArray())
        );
    }

    public function clearSettings()
    {
        Configuration::deleteByName(self::RESOURCE_KEY);
        Configuration::deleteByName(self::MAPPING_KEY);
    }
}
