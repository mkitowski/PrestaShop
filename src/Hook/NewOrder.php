<?php
namespace GetResponse\Hook;

use Configuration;
use GetResponse\Account\AccountSettings;
use GetResponse\Ecommerce\EcommerceServiceFactory;
use GetResponse\Order\OrderServiceFactory;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\Api\Exception\GetresponseApiException;
use Order;
use PrestaShopException;

/**
 * Class NewOrder
 * @package GetResponse\Hook
 */
class NewOrder
{
    /**
     * @param Order $order
     * @param AccountSettings $accountSettings
     * @throws ApiTypeException
     * @throws GetresponseApiException
     * @throws PrestaShopException
     */
    public function sendOrder(Order $order, AccountSettings $accountSettings)
    {
        if (empty($order) || 0 === (int)$order->id_customer) {
            return;
        }

        $ecommerce = EcommerceServiceFactory::create()->getEcommerceSettings();

        if (!$ecommerce->isEnabled()) {
            return;
        }

        $orderService = OrderServiceFactory::createFromSettings($accountSettings);
        $orderService->sendOrder($order, $ecommerce->getListId(), $ecommerce->getShopId());
    }
}
