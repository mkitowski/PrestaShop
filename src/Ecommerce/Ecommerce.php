<?php
/**
 * 2007-2020 PrestaShop
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
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace GetResponse\Ecommerce;

/**
 * Class Ecommerce
 * @package GetResponse\Ecommerce
 */
class Ecommerce
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /** @var string */
    private $status;

    /** @var string */
    private $shopId;

    /** @var string */
    private $listId;

    /**
     * @param string $status
     * @param string $shopId
     * @param string $listId
     */
    public function __construct($status, $shopId, $listId)
    {
        $this->status = $status;
        $this->shopId = $shopId;
        $this->listId = $listId;
    }

    /**
     * @param array $params
     * @return Ecommerce
     */
    public static function createFromPost(array $params)
    {
        if ($params['ecommerce']) {
            return new self(
                self::STATUS_ACTIVE,
                $params['shop'],
                $params['list']
            );
        } else {
            return new self(
                self::STATUS_INACTIVE,
                null,
                null
            );
        }
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
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
    public function getListId()
    {
        return $this->listId;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
