<?php
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
