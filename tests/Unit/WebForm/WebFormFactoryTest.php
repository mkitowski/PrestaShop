<?php
namespace GetResponse\Tests\UnitWebForm;

use GetResponse\Tests\Unit\BaseTestCase;
use GetResponse\WebForm\WebForm;
use GetResponse\WebForm\WebFormFactory;

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
            'formId' => 'webFormId',
            'status' => 'no',
            'sidebar' => 'left',
            'style' => 'myStyle',
            'url' => 'http://getresponse.com/webform/webFormId',
        ];

        $webForm = WebFormFactory::fromRequest($request);

        $this->assertEquals(
            new WebForm(
                $request['formId'],
                $request['status'],
                $request['sidebar'],
                $request['style'],
                $request['url']
            ), $webForm
        );
    }

    /**
     * @test
     */
    public function shouldCreateWebFormFromRequestWithDefaultValues()
    {
        $request = [
            'formId' => 'webFormId',
            'status' => '',
            'sidebar' => '',
            'style' => '',
            'url' => 'http://getresponse.com/webform/webFormId',
        ];

        $webForm = WebFormFactory::fromRequest($request);

        $this->assertEquals(
            new WebForm(
                $request['formId'],
                'no',
                'home',
                'webform',
                $request['url']
            ), $webForm
        );
    }

}
