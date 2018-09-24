<?php
namespace GetResponse\Hook;

use Currency;
use Customer;
use GetResponse\Account\AccountServiceFactory;
use GetResponse\Ecommerce\EcommerceServiceFactory;
use GetResponse\Order\OrderServiceFactory;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\GetresponseApiException;
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
     * @throws ApiTypeException
     * @throws GetresponseApiException
     */
    public function sendOrder(Order $order)
    {
        if (empty($order) || 0 === (int)$order->id_customer) {
            return;
        }

        $ecommerceService = EcommerceServiceFactory::create();
        if (!$ecommerceService->isEcommerceEnabled()) {
            return;
        }

        $grShopId = $ecommerceService->getEcommerceSettings()->getGetResponseShopId();

        $accountService = AccountServiceFactory::create();
        $contactListId = $accountService->getSettings()->getContactListId();

        $cartService = OrderServiceFactory::create();
        $cartService->sendOrder($order, $contactListId, $grShopId);
    }
}