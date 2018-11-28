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

namespace GetResponse\Export;

/**
 * Class ExportDto
 * @package GetResponse\Export
 */
class ExportSettings
{
    /** @var string */
    private $contactListId;

    /** @var int|null */
    private $cycleDay;

    /** @var bool */
    private $updateContactInfo;

    /** @var bool */
    private $newsletterSubsIncluded;

    /** @var bool */
    private $ecommerce;

    /** @var string */
    private $shopId;

    /**
     * @param string $contactListId
     * @param int|null $cycleDay
     * @param bool $updateContactInfo
     * @param bool $newsletterSubsIncluded
     * @param bool $ecommerce
     * @param string $shopId
     */
    public function __construct(
        $contactListId,
        $cycleDay,
        $updateContactInfo,
        $newsletterSubsIncluded,
        $ecommerce,
        $shopId
    ) {
        $this->contactListId = $contactListId;
        $this->cycleDay = $cycleDay;
        $this->updateContactInfo = $updateContactInfo;
        $this->newsletterSubsIncluded = $newsletterSubsIncluded;
        $this->ecommerce = $ecommerce;
        $this->shopId = $shopId;
    }

    /**
     * @return string
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @return string
     */
    public function getContactListId()
    {
        return $this->contactListId;
    }

    /**
     * @return int|null
     */
    public function getCycleDay()
    {
        return $this->cycleDay;
    }

    /**
     * @return bool
     */
    public function isUpdateContactInfo()
    {
        return $this->updateContactInfo;
    }

    /**
     * @return bool
     */
    public function isNewsletterSubsIncluded()
    {
        return $this->newsletterSubsIncluded;
    }

    /**
     * @return bool
     */
    public function isEcommerce()
    {
        return $this->ecommerce;
    }
}
