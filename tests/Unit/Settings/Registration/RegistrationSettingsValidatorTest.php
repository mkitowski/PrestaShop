<?php
namespace GetResponse\Tests\Unit\Settings\Registration;

use GetResponse\Settings\Registration\RegistrationSettings;
use GetResponse\Settings\Registration\RegistrationSettingsValidator;
use GetResponse\Tests\Unit\BaseTestCase;

/**
 * Class RegistrationSettingsValidatorTest
 * @package GetResponse\Tests\Unit\Settings\Registration
 */
class RegistrationSettingsValidatorTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldReturnNoError()
    {
        $registrationSettings = new RegistrationSettings(
            true,
            true,
            'contactListId',
            '0',
            false
        );

        $validator = new RegistrationSettingsValidator($registrationSettings);
        $this->assertTrue($validator->isValid());
        $this->assertEmpty($validator->getErrors());
    }

    /**
     * @test
     */
    public function shouldReturnError()
    {
        $registrationSettings = new RegistrationSettings(
            true,
            false,
            '',
            0,
            false
        );

        $validator = new RegistrationSettingsValidator($registrationSettings);
        $this->assertFalse($validator->isValid());
        $this->assertEquals(['You need to select list'], $validator->getErrors());
    }
}
