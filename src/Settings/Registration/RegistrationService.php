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

/**
 * Class RegistrationService
 */
class RegistrationService
{
    /** @var RegistrationRepository */
    private $registrationRepository;

    /**
     * @param RegistrationRepository $registrationRepository
     */
    public function __construct(RegistrationRepository $registrationRepository)
    {
        $this->registrationRepository = $registrationRepository;
    }

    /**
     * @return RegistrationSettings
     */
    public function getSettings()
    {
        return $this->registrationRepository->getSettings();
    }

    /**
     * @param RegistrationSettings $settings
     */
    public function updateSettings(RegistrationSettings $settings)
    {
        if ($settings->isActive()) {
            $this->registrationRepository->updateSettings($settings);
        } else {
            $this->clearSettings();
        }
    }

    public function clearSettings()
    {
        $this->registrationRepository->clearSettings();
    }
}
