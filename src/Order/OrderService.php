<?php
namespace GetResponse\Order;

use Address;
use Country;
use Currency;
use Customer;
use DateTime;
use GetResponse\Product\ProductService;
use GrShareCode\Address\Address as GrAddress;
use GrShareCode\CountryCodeConverter as GrCountryCodeConverter;
use GrShareCode\GetresponseApiException;
use GrShareCode\Order\AddOrderCommand as GrAddOrderCommand;
use GrShareCode\Order\Order as GrOrder;
use GrShareCode\Order\OrderService as GrOrderService;
use GrShareCode\Product\ProductsCollection;
use Order;
use OrderState;
use PrestaShopException;
use Product;
use Tools;

/**
 * Class OrderService
 * @package GetResponse\Order
 */
class OrderService
{
    /** @var GrOrderService */
    private $grOrderService;

    /**
     * @param GrOrderService $grOrderService
     */
    public function __construct(GrOrderService $grOrderService)
    {
        $this->grOrderService = $grOrderService;
    }

    /**
     * @param Order $order
     * @param string $contactListId
     * @param string $grShopId
     * @param bool $skipAutomation
     * @throws GetresponseApiException
     */
    public function sendOrder(Order $order, $contactListId, $grShopId, $skipAutomation = false)
    {
        $products = $order->getProducts();

        $productCollection = $this->getOrderProductsCollection($products);

        if (!$productCollection->getIterator()->count()) {
            return;
        }

        $grOrder = new GrOrder(
            (string)$order->id,
            $productCollection,
            (float)$order->total_paid_tax_excl,
            (float)($order->total_paid_tax_incl - $order->total_paid_tax_excl),
            Tools::getHttpHost(true) . __PS_BASE_URI__ . '?controller=order-detail&id_order=' . $order->id,
            (new Currency((int)$order->id_currency))->iso_code,
            $this->getOrderStatus($order),
            (string)$order->id_cart,
            '',
            (float)$order->total_shipping_tax_incl,
            $this->getOrderStatus($order),
            DateTime::createFromFormat('Y-m-d H:i:s', $order->date_add)->format(DateTime::ISO8601),
            $this->getOrderShippingAddress($order),
            $this->getOrderBillingAddress($order)
        );

        $email = (new Customer($order->id_customer))->email;

        $addOrderCommand = new GrAddOrderCommand($grOrder, $email, $contactListId, $grShopId);

        if ($skipAutomation) {
            $addOrderCommand->setToSkipAutomation();
        }

        $this->grOrderService->sendOrder($addOrderCommand);
    }

    /**
     * @param $products
     * @return ProductsCollection
     * @throws PrestaShopException
     */
    protected function getOrderProductsCollection($products)
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
                (int)$product['product_quantity']
            );

            $productsCollection->add($getresponseProduct);
        }

        return $productsCollection;
    }

    /**
     * @param Order $order
     * @return string
     */
    protected function getOrderStatus(Order $order)
    {
        $status = (new OrderState((int)$order->getCurrentState(), $order->id_lang))->name;

        return empty($status) ? 'new' : $status;
    }

    /**
     * @param Order $order
     * @return GrAddress
     */
    protected function getOrderShippingAddress(Order $order)
    {
        $address = new Address($order->id_address_delivery);
        $country = new Country($address->id_country);

        $grAddress = new GrAddress(
            GrCountryCodeConverter::getCountryCodeInISO3166Alpha3($country->iso_code),
            $address->firstname . ' ' . $address->lastname
        );
        $grAddress->setCountryName($address->country);
        $grAddress
            ->setFirstName($address->firstname)
            ->setLastName($address->lastname)
            ->setAddress1($address->address1)
            ->setAddress2($address->address2)
            ->setCity($address->city)
            ->setZip($address->postcode)
            ->setPhone($address->phone)
            ->setCompany($address->company);

        return $grAddress;
    }

    /**
     * @param Order $order
     * @return GrAddress
     */
    protected function getOrderBillingAddress(Order $order)
    {
        $address = new Address($order->id_address_invoice);
        $country = new Country($address->id_country);

        $grAddress = new GrAddress(
            GrCountryCodeConverter::getCountryCodeInISO3166Alpha3($country->iso_code),
            $address->firstname . ' ' . $address->lastname
        );
        $grAddress->setCountryName($address->country);
        $grAddress
            ->setFirstName($address->firstname)
            ->setLastName($address->lastname)
            ->setAddress1($address->address1)
            ->setAddress2($address->address2)
            ->setCity($address->city)
            ->setZip($address->postcode)
            ->setPhone($address->phone)
            ->setCompany($address->company);

        return $grAddress;
    }

}