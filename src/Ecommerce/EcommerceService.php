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
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace GetResponse\Ecommerce;

use GrShareCode\Api\Exception\GetresponseApiException;
use GrShareCode\Shop\Command\AddShopCommand;
use GrShareCode\Shop\Command\DeleteShopCommand;
use GrShareCode\Shop\ShopsCollection;
use GrShareCode\Shop\ShopService;

/**
 * Class EcommerceService
 * @package GetResponse\Ecommerce
 */
class EcommerceService
{
    /** @var EcommerceRepository */
    private $repository;

    /** @var ShopService */
    private $shopService;

    /**
     * @param EcommerceRepository $repository
     * @param ShopService $shopService
     */
    public function __construct(EcommerceRepository $repository, ShopService $shopService)
    {
        $this->repository = $repository;
        $this->shopService = $shopService;
    }

    /**
     * @return Ecommerce
     */
    public function getEcommerceSettings()
    {
        return $this->repository->getEcommerceSettings();
    }

    /**
     * @return ShopsCollection
     * @throws GetresponseApiException
     */
    public function getAllShops()
    {
        return $this->shopService->getAllShops();
    }

    /**
     * @param AddShopCommand $addShopCommand
     * @return string
     * @throws GetresponseApiException
     */
    public function createShop(AddShopCommand $addShopCommand)
    {
        return $this->shopService->addShop($addShopCommand);
    }

    /**
     * @param DeleteShopCommand $deleteShopCommand
     * @throws GetresponseApiException
     */
    public function deleteShop(DeleteShopCommand $deleteShopCommand)
    {
        $this->shopService->deleteShop($deleteShopCommand);
    }

    /**
     * @param Ecommerce $eCommerce
     */
    public function updateEcommerceDetails(Ecommerce $eCommerce)
    {
        if ($eCommerce->isEnabled()) {
            $this->repository->updateEcommerceSubscription($eCommerce);
        } else {
            $this->repository->clearEcommerceSettings();
        }
    }
}
