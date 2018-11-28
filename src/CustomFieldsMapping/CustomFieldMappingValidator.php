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

namespace GetResponse\CustomFieldsMapping;

use Translate;

/**
 * Class CustomFieldMappingValidator
 * @package GetResponse\ContactList
 */
class CustomFieldMappingValidator
{
    /** @var array */
    private $errors;

    /** @var array */
    private $requestData;

    /**
     * @param array $requestData
     */
    public function __construct(array $requestData)
    {
        $this->requestData = $requestData;
        $this->errors = [];
        $this->validate();
    }

    private function validate()
    {
        if (preg_match('/^[A-Za-z0-9]+$/', $this->requestData['gr_custom_id']) == false) {
            $this->errors[] = Translate::getAdminTranslation('Custom field contains invalid characters!');
        }

        if ($this->requestData['is_default']) {
            $this->errors[] = Translate::getAdminTranslation('Default mappings cannot be changed!');

            return;
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
