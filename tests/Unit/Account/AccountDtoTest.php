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

namespace GetResponse\Tests\Unit\Account;

use GetResponse\Account\AccountDto;
use GetResponse\Tests\Unit\BaseTestCase;

/**
 * Class AccountDtoTest
 * @package GetResponse\Tests\Unit\Account
 */
class AccountDtoTest extends BaseTestCase
{

    /**
     * @test
     */
    public function shouldCreateAccountDtoFromRequestForSmb()
    {
        $apiKey = 'apiKey';
        $enterprisePackage = '0';
        $accountType = 'AccountType';
        $domain = 'domain';

        $request = [
            'apiKey' => $apiKey,
            'enterprisePackage' => $enterprisePackage,
            'accountType' => $accountType,
            'domain' => $domain,
        ];

        $expected = new AccountDto(
            $apiKey,
            $enterprisePackage,
            'smb',
            ''
        );

        $accountDto =  AccountDto::fromRequest($request);
        $this->assertEquals($expected, $accountDto);
        $this->assertEquals('smb', $accountDto->getAccountTypeForSettings());
        $this->assertFalse($accountDto->isEnterprisePackage());
    }

    /**
     * @test
     */
    public function shouldCreateAccountDtoFromRequestForEnterprise()
    {
        $apiKey = 'apiKey';
        $enterprisePackage = '1';
        $accountType = 'AccountType';
        $domain = 'domain';

        $request = [
            'apiKey' => $apiKey,
            'enterprisePackage' => $enterprisePackage,
            'accountType' => $accountType,
            'domain' => $domain,
        ];

        $expected = new AccountDto(
            $apiKey,
            $enterprisePackage,
            $accountType,
            $domain
        );

        $accountDto =  AccountDto::fromRequest($request);
        $this->assertEquals($expected, $accountDto);
        $this->assertEquals($accountType, $accountDto->getAccountTypeForSettings());
        $this->assertTrue($accountDto->isEnterprisePackage());
    }
}
