<?php
namespace GetResponse\Hook;

use Cart;
use Currency;
use Customer;
use GetResponse\Account\AccountServiceFactory;
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
     * @throws GetresponseApiException
     * @throws ApiTypeException
     */
    public function sendCart($cart)
    {
        if (empty($cart) || 0 == $cart->id_customer) {
            return;
        }

        $ecommerceService = EcommerceServiceFactory::create();
        if (!$ecommerceService->isEcommerceEnabled()) {
            return;
        }

        $grShopId = $ecommerceService->getEcommerceSettings()->getGetResponseShopId();

        $accountService = AccountServiceFactory::create();
        $contactListId = $accountService->getSettings()->getContactListId();

        $cartService = CartServiceFactory::create();
        $cartService->sendCart($cart, $contactListId, $grShopId);
    }


}