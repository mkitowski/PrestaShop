<?php
namespace GetResponse\Ecommerce;

use GetResponse\Settings\Settings;
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

    /** @var Settings */
    private $settings;

    /**
     * @param EcommerceRepository $repository
     * @param ShopService $shopService
     */
    public function __construct(EcommerceRepository $repository, ShopService $shopService, Settings $settings)
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
     */
    public function getAllShops()
    {
        return $this->shopService->getAllShops();
    }

    /**
     * @param AddShopCommand $addShopCommand
     * @return string
     */
    public function createShop(AddShopCommand $addShopCommand)
    {
        return $this->shopService->addShop($addShopCommand);
    }

    /**
     * @param string $shopId
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
     * @param string $grShopId
     * @param Activity $activity
     */
    public function updateEcommerceDetails($grShopId, Activity $activity)
    {
        $this->repository->updateEcommerceSubscription($activity);

        if ($activity->isEnabled()) {
            $this->repository->updateEcommerceShopId($grShopId);
        }
    }
}