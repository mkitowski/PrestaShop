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

namespace GetResponse\Account;

use Tools;

/**
 * Class AccountSettings
 * @package GetResponse\Account
 */
class AccountSettings
{
    const ACCOUNT_TYPE_SMB = 'smb';
    const ACCOUNT_TYPE_360_US = 'mx_us';
    const ACCOUNT_TYPE_360_PL = 'mx_pl';

    /** @var string */
    private $apiKey;

    /** @var string */
    private $accountType;

    /** @var string */
    private $domain;

    /**
     * @param string $apiKey
     * @param string $accountType
     * @param string $domain
     */
    public function __construct(
        $apiKey,
        $accountType,
        $domain
    ) {
        $this->apiKey = $apiKey;
        $this->accountType = $accountType;
        $this->domain = $domain;
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
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return bool
     */
    public function isConnectedWithGetResponse()
    {
        return !empty($this->getApiKey());
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return null|string
     */
    public function getHiddenApiKey()
    {
        if (Tools::strlen($this->getApiKey()) > 0) {
            return str_repeat('*', Tools::strlen($this->getApiKey()) - 6) . Tools::substr($this->getApiKey(), -6);
        }

        return null;
    }

    /**
     * @param array $params
     * @return AccountSettings
     */
    public static function createFromSettings($params)
    {
        return new self($params['api_key'], $params['type'], $params['domain']);
    }

    /**
     * @return AccountSettings
     */
    public static function createEmptyInstance()
    {
        return new self('', self::ACCOUNT_TYPE_SMB, '');
    }
}
