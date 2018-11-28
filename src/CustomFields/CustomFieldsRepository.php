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

namespace GetResponse\CustomFields;

use Configuration;
use GetResponse\CustomFieldsMapping\CustomFieldMapping;
use GetResponse\CustomFieldsMapping\CustomFieldMappingCollection;

class CustomFieldsRepository
{
    const RESOURCE_KEY = 'getresponse_customs';

    /**
     * @return CustomFieldMappingCollection
     */
    public function getCustomFieldsMapping()
    {
        $collection = new CustomFieldMappingCollection();

        $result = json_decode(Configuration::get(self::RESOURCE_KEY), true);

        if (empty($result)) {
            return $collection;
        }

        foreach ($result as $row) {
            $collection->add(new CustomFieldMapping(
                $row['id'],
                $row['custom_name'],
                $row['customer_property_name'],
                $row['gr_custom_id'],
                $row['is_active'],
                $row['is_default']
            ));
        }

        return $collection;
    }

    public function clearCustomFields()
    {
        Configuration::updateValue(self::RESOURCE_KEY, null);
    }

    /**
     * @param CustomFieldMappingCollection $collection
     * @param int|null $storeId
     */
    public function updateCustomFields(CustomFieldMappingCollection $collection, $storeId = null)
    {
        Configuration::updateValue(self::RESOURCE_KEY, json_encode($collection->toArray()), false, null, $storeId);
    }
}
