<?php
namespace GetResponse\Tests\Unit\Account;

use GetResponse\Account\AccountDto;
use GetResponse\Account\AccountValidator;
use GetResponse\Tests\Unit\BaseTestCase;

/**
 * Class AccountValidatorTest
 * @package GetResponse\Tests\Unit\Account
 */
class AccountValidatorTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldNotReturnValidationError()
    {
        $accountDto = new AccountDto(
            'apiKey',
            '1',
            '360pl',
            'www.getresponse.com'
        );

        $accountValidator = new AccountValidator($accountDto);

        $this->assertTrue($accountValidator->isValid());
        $this->assertEquals([], $accountValidator->getErrors());
    }

    /**
     * @test
     */
    public function shouldReturnValidationError()
    {
        // empty api key
        $accountDto = new AccountDto(
            '',
            '1',
            '360pl',
            'www.getresponse.com'
        );

        $accountValidator = new AccountValidator($accountDto);

        $this->assertFalse($accountValidator->isValid());
        $errorMessage = 'You need to enter API key. This field can\'t be empty.';
        $this->assertEquals([$errorMessage], $accountValidator->getErrors());

        // empty account type
        $accountDto = new AccountDto(
            'apiKey',
            '1',
            '',
            'www.getresponse.com'
        );

        $accountValidator = new AccountValidator($accountDto);

        $this->assertFalse($accountValidator->isValid());
        $errorMessage = 'Invalid account type.';
        $this->assertEquals([$errorMessage], $accountValidator->getErrors());

        // empty domain
        $accountDto = new AccountDto(
            'apiKey',
            '1',
            '360us',
            ''
        );

        $accountValidator = new AccountValidator($accountDto);

        $this->assertFalse($accountValidator->isValid());
        $errorMessage = 'Domain field can not be empty.';
        $this->assertEquals([$errorMessage], $accountValidator->getErrors());

    }
}
