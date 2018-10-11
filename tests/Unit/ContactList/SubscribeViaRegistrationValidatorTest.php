<?php
namespace GetResponse\Tests\Unit\ContactList;

use GetResponse\ContactList\SubscribeViaRegistrationDto;
use GetResponse\ContactList\SubscribeViaRegistrationValidator;
use GetResponse\Tests\Unit\BaseTestCase;

class SubscribeViaRegistrationValidatorTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldReturnNoError()
    {
        $dto = new SubscribeViaRegistrationDto(
            '1',
            '1',
            'contactListId',
            '0',
            '',
            '1'
        );

        $validator = new SubscribeViaRegistrationValidator($dto);
        $this->assertTrue($validator->isValid());
        $this->assertEmpty($validator->getErrors());
    }

    /**
     * @test
     */
    public function shouldReturnError()
    {
        $dto = new SubscribeViaRegistrationDto(
            '1',
            '1',
            '',
            '0',
            '',
            '1'
        );

        $validator = new SubscribeViaRegistrationValidator($dto);
        $this->assertFalse($validator->isValid());
        $this->assertEquals(['You need to select list'], $validator->getErrors());
    }
}
