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
            ),
            $webForm
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
            ),
            $webForm
        );
    }
}
