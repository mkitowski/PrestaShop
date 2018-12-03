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
            '',
            '',
            '',
            ''
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
