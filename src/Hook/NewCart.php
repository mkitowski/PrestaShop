<?php
namespace GetResponse\Hook;

use Cart;
use Currency;
use Customer;
use GetResponse\Account\AccountSettings;
use GetResponse\Cart\CartServiceFactory;
use GetResponse\Ecommerce\EcommerceServiceFactory;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\GetresponseApiException;
use PrestaShopException;

/**
 * Class NewCart
 * @package GetResponse\Hook
 */
class NewCart
{
    /**
     * @param Cart $cart
     * @param AccountSettings $accountSettings
     * @throws ApiTypeException
     * @throws GetresponseApiException
     */
    public function sendCart($cart, AccountSettings $accountSettings)
    {
        if (null === $cart || 0 == $cart->id_customer) {
            return;
        }

        $ecommerceService = EcommerceServiceFactory::createFromSettings($accountSettings);
        if (!$ecommerceService->isEcommerceEnabled()) {
            return;
        }

        $grShopId = $ecommerceService->getEcommerceSettings()->getGetResponseShopId();
        $contactListId = $accountSettings->getContactListId();

        $cartService = CartServiceFactory::createFromAccountSettings($accountSettings);
        $cartService->sendCart($cart, $contactListId, $grShopId);
    }

}