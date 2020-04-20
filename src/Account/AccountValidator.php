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

namespace GetResponse\Account;

use Translate;

/**
 * Class AccountValidator
 * @package GetResponse\Account
 */
class AccountValidator
{
    /** @var array */
    private $errors;

    /** @var AccountDto */
    private $accountDto;

    /**
     * @param AccountDto $accountDto
     */
    public function __construct(AccountDto $accountDto)
    {
        $this->accountDto = $accountDto;
        $this->errors = [];
        $this->validate();
    }

    private function validate()
    {
        if (empty($this->accountDto->getApiKey())) {
            $this->errors[] = Translate::getAdminTranslation('You need to enter API key. This field can\'t be empty.');

            return;
        }

        if ($this->accountDto->isEnterprisePackage() && empty($this->accountDto->getAccountType())) {
            $this->errors[] = Translate::getAdminTranslation('Invalid account type.');

            return;
        }

        if ($this->accountDto->isEnterprisePackage() && empty($this->accountDto->getDomain())) {
            $this->errors[] = Translate::getAdminTranslation('Domain field can not be empty.');

            return;
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
