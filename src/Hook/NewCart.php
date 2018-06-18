<?php
namespace GetResponse\Hook;

use GetResponse\Ecommerce\EcommerceRepository;
use GetResponse\Ecommerce\EcommerceService;
use GrShareCode\GetresponseApi;
use GetResponse\Account\AccountServiceFactory as GrAccountServiceFactory;
use Currency;
use GrShareCode\Shop\ShopService;
use GetResponseRepository;
use GrShareCode\Product\ProductService as GrProductService;
use Customer;
use Db;
use Cart;
use GrShareCode\GetresponseApiException;
use PrestaShopException;
use GrShareCode\Cart\Cart as GrCart;
use GrShareCode\Cart\CartService as GrCartService;
use GrShareCode\Cart\AddCartCommand as GrAddCartCommand;

/**
 * Class NewCart
 * @package GetResponse\Hook
 */
class NewCart extends Hook
{
    /** @var GetresponseApi */
    private $api;

    /** @var Db */
    private $db;

    /** @var GetResponseRepository */
    private $repository;

    /**
     * @param GetresponseApi $api
     * @param GetResponseRepository $repository
     * @param Db $db
     */
    public function __construct(GetresponseApi $api, GetResponseRepository $repository, Db $db)
    {
        $this->api = $api;
        $this->db = $db;
        $this->repository = $repository;
    }

    /**
     * @param Cart $cart
     * @throws GetresponseApiException
     * @throws PrestaShopException
     */
    public function sendCart(Cart $cart)
    {
        if (0 == $cart->id_customer) {
            return;
        }

        $accountService = GrAccountServiceFactory::create();
        $settings = $accountService->getSettings();
        $ecommerceService = new EcommerceService(
            new EcommerceRepository($this->db, $settings->getShopId()),
            new ShopService($this->api),
            $settings
        );

        if (empty($ecommerceService->getEcommerceSettings()->getGetResponseShopId())) {
            return;
        }

        $customer = new Customer($cart->id_customer);
        $productService = new GrProductService($this->api, $this->repository);
        $cartService = new GrCartService($this->api, $this->repository,
            $productService);

        $grCart = new GrCart(
            (string)$cart->id,
            $this->getOrderProductsCollection($cart),
            (new Currency((int)$cart->id_currency))->iso_code,
            floatval($cart->getOrderTotal(false)),
            floatval($cart->getOrderTotal(true))
        );

        $cartService->sendCart(
            new GrAddCartCommand(
                $grCart,
                $customer->email,
                $settings->getCampaignId(),
                $settings->getShopId()
            )
        );
    }

}