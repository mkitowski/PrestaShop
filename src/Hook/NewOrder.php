<?php
namespace GetResponse\Hook;

use Currency;
use Customer;
use GetResponse\Account\AccountSettings;
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
     * @param AccountSettings $accountSettings
     * @throws ApiTypeException
     * @throws GetresponseApiException
     */
    public function sendOrder(Order $order, AccountSettings $accountSettings)
    {
        if (empty($order) || 0 === (int)$order->id_customer) {
            return;
        }

        $ecommerceService = EcommerceServiceFactory::createFromSettings($accountSettings);
        if (!$ecommerceService->isEcommerceEnabled()) {
            return;
        }

        $grShopId = $ecommerceService->getEcommerceSettings()->getGetResponseShopId();
        $contactListId = $accountSettings->getContactListId();

        $cartService = OrderServiceFactory::createFromSettings($accountSettings);
        $cartService->sendOrder($order, $contactListId, $grShopId);
    }
}