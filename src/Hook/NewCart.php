<?php
namespace GetResponse\Hook;

use Cart;
use Configuration;
use GetResponse\Account\AccountSettings;
use GetResponse\Cart\CartServiceFactory;
use GetResponse\Ecommerce\EcommerceServiceFactory;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\Api\Exception\GetresponseApiException;

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

        $ecommerce = EcommerceServiceFactory::create()->getEcommerceSettings();

        if (!$ecommerce->isEnabled()) {
            return;
        }

        $cartService = CartServiceFactory::createFromAccountSettings($accountSettings);
        $cartService->sendCart($cart, $ecommerce->getListId(), $ecommerce->getShopId());
    }

}
