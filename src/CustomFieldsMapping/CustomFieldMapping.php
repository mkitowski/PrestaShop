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

/**
 * Class CustomFieldMapping
 * @package GetResponse\CustomFieldsMapping
 */
class CustomFieldMapping
{
    /** @var int */
    private $id;

    /** @var string */
    private $customName;

    /** @var string */
    private $customerPropertyName;

    /** @var string */
    private $grCustomId;

    /** @var bool */
    private $isActive;

    /** @var bool */
    private $isDefault;

    /**
     * @param int $id
     * @param string $customName
     * @param string $customerPropertyName
     * @param string $grCustomId
     * @param bool $isActive
     * @param bool $isDefault
     */
    public function __construct($id, $customName, $customerPropertyName, $grCustomId, $isActive, $isDefault)
    {
        $this->id = $id;
        $this->customName = $customName;
        $this->customerPropertyName = $customerPropertyName;
        $this->grCustomId = $grCustomId;
        $this->isActive = $isActive;
        $this->isDefault = $isDefault;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->isDefault;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCustomerPropertyName()
    {
        return $this->customerPropertyName;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return string
     */
    public function getCustomName()
    {
        return $this->customName;
    }

    /**
     * @return string
     */
    public function getGrCustomId()
    {
        return $this->grCustomId;
    }

    /**
     * @param array $params
     * @return CustomFieldMapping
     */
    public static function createFromArray(array $params)
    {
        return new self(
            $params['id'],
            $params['custom_name'],
            $params['customer_property_name'],
            $params['gr_custom_id'],
            (bool) $params['is_active'],
            (bool) $params['is_default']
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'custom_name' => $this->getCustomName(),
            'customer_property_name' => $this->getCustomerPropertyName(),
            'gr_custom_id' => $this->getGrCustomId(),
            'is_active' => $this->isActive(),
            'is_default' => $this->isDefault(),
        ];
    }
}
