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

        $ecommerceService = EcommerceServiceFactory::createFromSettings($accountSettings);
        if (!$ecommerceService->isEcommerceEnabled()) {
            return;
        }

        // @TODO move this code to repository and use service in this place.
        $registrationSettings = json_decode(Configuration::get(\ConfigurationSettings::REGISTRATION), true);


        $orderService = OrderServiceFactory::createFromSettings($accountSettings);
        $orderService->sendOrder(
            $order,
            $registrationSettings['campaign_id'],
            $ecommerceService->getEcommerceSettings()->getShopId()
        );
    }
}
