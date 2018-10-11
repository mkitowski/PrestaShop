<?php

namespace GetResponse\Cart;

use Cart;
use Currency;
use Customer;
use GetResponse\Product\ProductService;
use GrShareCode\Cart\AddCartCommand as GrAddCartCommand;
use GrShareCode\Cart\Cart as GrCart;
use GrShareCode\Cart\CartService as GrCartService;
use GrShareCode\GetresponseApiException;
use GrShareCode\Product\ProductsCollection;
use Product;

/**
 * Class CartService
 */
class CartService
{
    /** @var GrCartService */
    private $grCartService;

    /**
     * @param GrCartService $grCartService
     */
    public function __construct(GrCartService $grCartService)
    {
        $this->grCartService = $grCartService;
    }

    /**
     * @param Cart $cart
     * @param string $contactListId
     * @param string $grShopId
     * @throws GetresponseApiException
     */
    public function sendCart(Cart $cart, $contactListId, $grShopId)
    {
        $products = $cart->getProducts();

        $productCollection = $this->getOrderProductsCollection($products);

        $grCart = new GrCart(
            (string)$cart->id,
            $productCollection,
            (new Currency((int)$cart->id_currency))->iso_code,
            $cart->getOrderTotal(false),
            $cart->getOrderTotal(true)
        );

        $customer = new Customer($cart->id_customer);
        $email = $customer->email;

        $this->grCartService->sendCart(
            new GrAddCartCommand($grCart, $email, $contactListId, $grShopId)
        );
    }

    /**
     * @param array $products
     * @return ProductsCollection
     * @throws PrestaShopException
     */
    private function getOrderProductsCollection(array $products)
    {
        $productsCollection = new ProductsCollection();

        foreach ($products as $product) {

            $prestashopProduct = new Product($product['id_product']);

            if (empty($prestashopProduct->reference)) {
                continue;
            }
            $productService = new ProductService();

            $getresponseProduct = $productService->createProductFromPrestaShopProduct(
                $prestashopProduct,
                $product['quantity']
            );

            $productsCollection->add($getresponseProduct);
        }

        return $productsCollection;
    }
}