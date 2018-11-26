<?php
namespace GetResponse\Tests\Unit\WebForm;

use GetResponse\Tests\Unit\BaseTestCase;
use GetResponse\WebForm\WebForm;

/**
 * Class WebFormFactoryTest
 * @package GetResponse\Tests\UnitWebForm
 */
class WebFormFactoryTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateWebFormFromRequest()
    {
        $request = [
            'form' => 'webFormId',
            'subscription' => '1',
            'position' => 'left',
            'style' => 'myStyle'
        ];

        $webForm = WebForm::createFromPost($request);

        $this->assertEquals(
            new WebForm(
                WebForm::STATUS_ACTIVE,
                $request['form'],
                $request['position'],
                $request['style']
            ), $webForm
        );
    }

    /**
     * @test
     */
    public function shouldCreateWebFormFromRequestWithDefaultValues()
    {
        $request = [
            'form' => '',
            'subscription' => '0',
            'position' => '',
            'style' => '',
        ];

        $webForm = WebForm::createFromPost($request);

        $this->assertEquals(
            new WebForm(
                WebForm::STATUS_INACTIVE,
                null,
                '',
                ''
            ), $webForm
        );
    }

}
