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

/**
 * Class AccountDto
 * @package GetResponse\Account
 */
class AccountDto
{
    /** @var string */
    private $apiKey;

    /** @var string */
    private $isEnterprisePackage;

    /** @var string */
    private $accountType;

    /** @var string */
    private $domain;

    /**
     * @param string $apiKey
     * @param bool $isEnterprisePackage
     * @param string $accountType
     * @param string $domain
     */

    public function __construct($apiKey, $isEnterprisePackage, $accountType, $domain)
    {
        $this->apiKey = $apiKey;
        $this->isEnterprisePackage = $isEnterprisePackage;
        $this->accountType = $accountType;
        $this->domain = $domain;
    }

    /**
     * @param array $request
     * @return AccountDto
     */
    public static function fromRequest(array $request)
    {
        $type = (bool) $request['enterprisePackage'] ? $request['accountType'] : AccountSettings::ACCOUNT_TYPE_SMB;
        return new self(
            $request['apiKey'],
            (bool) $request['enterprisePackage'],
            $type,
            (bool) $request['enterprisePackage'] ? $request['domain'] : ''
        );
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * @return string
     */
    public function getAccountTypeForSettings()
    {
        return $this->isEnterprisePackage() ? $this->accountType : AccountSettings::ACCOUNT_TYPE_SMB;
    }

    /**
     * @return bool
     */
    public function isEnterprisePackage()
    {
        return $this->isEnterprisePackage;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }
}
