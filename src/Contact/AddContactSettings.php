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

namespace GetResponse\Contact;

use GetResponse\Settings\Registration\RegistrationSettings;

/**
 * Class AddContactSettings
 * @package GetResponse\Contact
 */
class AddContactSettings
{
    /** @var string */
    private $contactListId;

    /** @var string */
    private $dayOfCycle;

    /** @var bool */
    private $updateContactCustomFields;

    /**
     * @param string $contactListId
     * @param string $dayOfCycle
     * @param bool $updateContactCustomFields
     */
    public function __construct($contactListId, $dayOfCycle, $updateContactCustomFields)
    {
        $this->contactListId = $contactListId;
        $this->dayOfCycle = $dayOfCycle;
        $this->updateContactCustomFields = $updateContactCustomFields;
    }

    /**
     * @param RegistrationSettings $settings
     * @return AddContactSettings
     */
    public static function createFromConfiguration(RegistrationSettings $settings)
    {
        return new self(
            $settings->getListId(),
            $settings->getCycleDay(),
            $settings->isUpdateContactEnabled()
        );
    }

    /**
     * @return string
     */
    public function getContactListId()
    {
        return $this->contactListId;
    }

    /**
     * @return string
     */
    public function getDayOfCycle()
    {
        return $this->dayOfCycle;
    }

    /**
     * @return bool
     */
    public function isUpdateContactCustomFields()
    {
        return $this->updateContactCustomFields;
    }
}
