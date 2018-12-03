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

namespace GetResponse\Tests\Unit\CustomFields;

use GetResponse\CustomFields\CustomFieldService;
use GetResponse\CustomFields\CustomFieldsRepository;
use GetResponse\Tests\Unit\BaseTestCase;
use GrShareCode\CustomField\CustomField;
use GrShareCode\CustomField\CustomFieldCollection;
use GrShareCode\CustomField\CustomFieldService as GrCustomFieldService;

class CustomFieldServiceTest extends BaseTestCase
{
    /** @var CustomFieldsRepository | \PHPUnit_Framework_MockObject_MockObject */
    private $customFieldsRepository;

    /** @var CustomFieldService */
    private $sut;

    protected function setUp()
    {
        $this->customFieldsRepository = $this->getMockWithoutConstructing(CustomFieldsRepository::class);
        $this->sut = new CustomFieldService($this->customFieldsRepository);
    }

    /**
     * @test
     */
    public function shouldReturnCustomFields()
    {
        $collection = new CustomFieldCollection();
        $collection->add(new CustomField('d4s2', 'testCustom', 'text', 'null'));

        $this->customFieldsRepository
            ->expects(self::once())
            ->method('getCustomFieldsMapping')
            ->willReturn($collection);

        self::assertEquals($collection, $this->sut->getCustomFieldsMapping());
    }
}
