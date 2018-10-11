<?php
namespace GetResponse\Ecommerce;

use GetResponse\Account\AccountSettings;
use GrShareCode\GetresponseApiException;
use GrShareCode\Shop\AddShopCommand;
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

    /** @var AccountSettings */
    private $settings;

    /**
     * @param EcommerceRepository $repository
     * @param ShopService $shopService
     * @param AccountSettings $settings
     */
    public function __construct(EcommerceRepository $repository, ShopService $shopService, AccountSettings $settings)
    {
        $this->repository = $repository;
        $this->shopService = $shopService;
        $this->settings = $settings;
    }

    /**
     * @return Ecommerce|null
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
     * @param string $shopId
     * @throws GetresponseApiException
     */
    public function deleteShop($shopId)
    {
        $this->shopService->deleteShop($shopId);
    }

    /**
     * @return bool
     */
    public function isEcommerceEnabled()
    {
        return $this->getEcommerceSettings() !== null;
    }

    /**
     * @return bool
     */
    public function isSubscribeViaRegistrationActive()
    {
        return $this->settings->isSubscriptionActive();
    }

    /**
     * @param EcommerceDto $ecommerceDto
     */
    public function updateEcommerceDetails(EcommerceDto $ecommerceDto)
    {
        $this->repository->updateEcommerceSubscription($ecommerceDto->isEnabled());

        if ($ecommerceDto->isEnabled()) {
            $this->repository->updateEcommerceShopId($ecommerceDto->getShopId());
        }
    }
}