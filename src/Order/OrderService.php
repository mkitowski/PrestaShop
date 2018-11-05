<?php
namespace GetResponse\Order;

use Customer;
use GetResponse\Product\ProductFactory;
use GrShareCode\GetresponseApiException;
use GrShareCode\Order\Command\AddOrderCommand as GrAddOrderCommand;
use GrShareCode\Order\OrderService as GrOrderService;
use GrShareCode\Product\ProductsCollection;
use Order;
use PrestaShopException;
use Product;

/**
 * Class OrderService
 * @package GetResponse\Order
 */
class OrderService
{
    /** @var GrOrderService */
    private $grOrderService;
    /** @var OrderFactory */
    private $orderFactory;

    /**
     * @param GrOrderService $grOrderService
     * @param OrderFactory $orderFactory
     */
    public function __construct(GrOrderService $grOrderService, OrderFactory $orderFactory)
    {
        $this->grOrderService = $grOrderService;
        $this->orderFactory = $orderFactory;
    }

    /**
     * @param Order $order
     * @param string $contactListId
     * @param string $grShopId
     * @throws GetresponseApiException
     * @throws PrestaShopException
     */
    public function sendOrder(Order $order, $contactListId, $grShopId)
    {
        $productCollection = $this->getOrderProductsCollection($order->getProducts());

        if (!$productCollection->getIterator()->count()) {
            return;
        }

        $addOrderCommand = new GrAddOrderCommand(
            $this->orderFactory->createShareCodeOrderFromOrder($order),
            (new Customer($order->id_customer))->email,
            $contactListId,
            $grShopId
        );

        $this->grOrderService->addOrder($addOrderCommand);
    }

    /**
     * @param $products
     * @return ProductsCollection
     * @throws PrestaShopException
     */
    private function getOrderProductsCollection($products)
    {
        $productsCollection = new ProductsCollection();

        foreach ($products as $product) {

            $prestashopProduct = new Product($product['id_product']);

            if (empty($prestashopProduct->reference)) {
                continue;
            }
            $productService = new ProductFactory();
            $getresponseProduct = $productService->createShareCodeProductFromProduct(
                $prestashopProduct,
                (int)$product['product_quantity']
            );

            $productsCollection->add($getresponseProduct);
        }

        return $productsCollection;
    }
}