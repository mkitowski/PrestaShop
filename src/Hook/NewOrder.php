<?php
namespace GetResponse\Hook;

use GetResponse\Ecommerce\EcommerceRepository;
use GetResponse\Ecommerce\EcommerceService;
use GrShareCode\GetresponseApi;
use GetResponse\Account\AccountServiceFactory as GrAccountServiceFactory;
use DateTime;
use Currency;
use GrShareCode\Shop\ShopService;
use Tools;
use GrShareCode\Order\Order as GrOrder;
use GrShareCode\Order\AddOrderCommand as GrAddOrderCommand;
use GrShareCode\Order\OrderService as GrOrderService;
use GetResponseRepository;
use GrShareCode\Product\ProductService as GrProductService;
use Order;
use Customer;
use Db;
use GrShareCode\GetresponseApiException;
use PrestaShopException;

/**
 * Class NewOrder
 * @package GetResponse\Hook
 */
class NewOrder extends Hook
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
     */
    public function __construct(GetresponseApi $api, GetResponseRepository $repository, Db $db)
    {
        $this->api = $api;
        $this->db = $db;
        $this->repository = $repository;
    }

    /**
     * @param Order $order
     * @throws GetresponseApiException
     * @throws PrestaShopException
     */
    public function sendOrder(Order $order)
    {
        if (empty($order) || 0 === (int)$order->id_customer) {
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

        $grOrder = new GrOrder(
            $order->id,
            $this->getOrderProductsCollection($order),
            floatval($order->total_paid_tax_excl),
            floatval($order->total_paid_tax_incl),
            Tools::getHttpHost(true) . __PS_BASE_URI__ . '?controller=order-detail&id_order=' . $order->id,
            (new Currency((int)$order->id_currency))->iso_code,
            $this->getOrderStatus($order),
            (string)$order->id_cart,
            '',
            floatval($order->total_shipping_tax_incl),
            $this->getOrderStatus($order),
            DateTime::createFromFormat('Y-m-d H:i:s', $order->date_add),
            $this->getOrderShippingAddress($order),
            $this->getOrderBillingAddress($order)
        );

        $addOrderCommand = new GrAddOrderCommand(
            $grOrder,
            (new Customer($order->id_customer))->email,
            $settings->getCampaignId(),
            $ecommerceService->getEcommerceSettings()->getGetResponseShopId()
        );

        $orderService = new GrOrderService(
            $this->api,
            $this->repository,
            new GrProductService($this->api, $this->repository)
        );

        $orderService->sendOrder($addOrderCommand);
        //@TODO remove cart
    }

}