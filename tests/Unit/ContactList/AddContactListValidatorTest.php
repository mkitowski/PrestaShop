<?php
namespace GetResponse\Tests\Unit\ContactList;

use GetResponse\ContactList\AddContactListDto;
use GetResponse\ContactList\AddContactListValidator;
use GetResponse\Tests\Unit\BaseTestCase;

/**
 * Class AddContactListValidatorTest
 * @package GetResponse\Tests\Unit\ContactList
 */
class AddContactListValidatorTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldReturnNoError()
    {
        $dto = new AddContactListDto(
            'contactListName',
            'fromField',
            'replyTo',
            'subjectId',
            'bodyId'
        );

        $validator = new AddContactListValidator($dto);
        $this->assertTrue($validator->isValid());
        $this->assertEmpty($validator->getErrors());
    }

    /**
     * @test
     */
    public function shouldReturnError()
    {
        $dto = new AddContactListDto(
            'con',
            'fro',
            'rep',
            'sub',
            'bod'
        );

        $validator = new AddContactListValidator($dto);
        $this->assertFalse($validator->isValid());
        $this->assertEquals([
            'The "list name" field is invalid',
            'The "from" field is required',
            'The "reply-to" field is required',
            'The "confirmation subject" field is required',
            'The "confirmation body" field is required',
        ], $validator->getErrors());
    }
}
