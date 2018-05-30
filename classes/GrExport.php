<?php

use GrShareCode\Contact\ContactService as GrContactService;
use GrShareCode\GetresponseApi;
use GrShareCode\Api\ApiType as GrApiType;
use GrShareCode\Contact\CustomFieldsCollection as GrCustomFieldsCollection;
use GrShareCode\Contact\CustomField as GrCustomField;
use GrShareCode\GetresponseApiException;
use GrShareCode\Api\ApiTypeException as GrApiTypeException;
use GrShareCode\Contact\AddContactCommand as GrAddContactCommand;
use GrShareCode\Cart\Cart as GrCart;
use GrShareCode\Product\ProductsCollection as GrProductsCollection;
use GrShareCode\Product\Category\CategoryCollection as GrCategoryCollection;
use GrShareCode\Product\Category\Category as GrCategory;
use GrShareCode\Product\Variant\Variant as GrVariant;
use GrShareCode\Product\Product as GrProduct;
use GrShareCode\Cart\AddCartCommand as GrAddCartCommand;
use GrShareCode\Product\ProductService as GrProductService;
use GrShareCode\Cart\CartService as GrCartService;
use GrShareCode\Export\Settings\EcommerceSettings as GrEcommerceSettings;
use GrShareCode\Export\Settings\ExportSettings as GrExportSettings;
use GrShareCode\Order\OrderService as GrOrderService;
use GrShareCode\Export\ExportCustomersService as GrExportCustomersService;
use GrShareCode\Export\ExportContactService as GrExportContactService;
use GrShareCode\Export\ExportContactCommand as GrExportContactCommand;
use GrShareCode\Export\Settings\ExportSettings;
use GrShareCode\Export\HistoricalOrder\HistoricalOrderCollection as GrHistoricalOrderCollection;
use GrShareCode\Export\HistoricalOrder\HistoricalOrder as GrHistoricalOrder;
use GrShareCode\Address\Address as GrAddress;

class GrExport
{
    /** @var GetResponseExportSettings */
    private $exportSettings;

    /** @var GetResponseRepository */
    private $repository;

    /**
     * @param GetResponseExportSettings $exportSettings
     * @param GetResponseRepository $getResponseRepository
     */
    public function __construct(
        GetResponseExportSettings $exportSettings,
        GetResponseRepository $getResponseRepository
    ) {
        $this->exportSettings = $exportSettings;
        $this->repository = $getResponseRepository;
    }

    /**
     * @throws GetresponseApiException
     * @throws GrApiTypeException
     * @throws GrGeneralException
     * @throws PrestaShopDatabaseException
     */
    public function export()
    {
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $dbSettings = $repository->getSettings();
        $api = new GetresponseApi($dbSettings['api_key'], GrApiType::createForSMB(), 'xapp');
        $contactService = new GrContactService($api);
        $productService = new GrProductService($api, $repository);
        $cartService = new GrCartService($api, $repository, $productService);
        $orderService = new GrOrderService($api, $repository, $productService);
        $exportService = new GrExportContactService($contactService, $cartService, $orderService);
        $contacts = $repository->getContacts($this->exportSettings->isNewsletter());
        $grCustoms = $contactService->getAllCustomFields();
        $settings = new GrExportSettings(
            $this->exportSettings->getListId(),
            $this->exportSettings->getCycleDay(),
            $this->exportSettings->isAsyncExport(),
            $this->exportSettings->isUpdateAddress(),
            new GrEcommerceSettings(
                $this->exportSettings->isExportEcommerce(),
                GrShop::getUserShopId()
            )
        );

        foreach ($contacts as $contact) {
            $orders = new GrHistoricalOrderCollection();
            $customerOrders = $this->repository->getOrders($contact['id']);

            foreach ($customerOrders as $customerOrder) {
                $orderCore = new Order($customerOrder['id_order']);
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $orderCore->date_add);
                $orders->add(new GrHistoricalOrder(
                    (int) $customerOrder['id_order'],
                    $this->getOrderProductsCollection($orderCore),
                    floatval($orderCore->total_paid_tax_excl),
                    floatval($orderCore->total_paid_tax_incl),
                    Tools::getHttpHost(true) . __PS_BASE_URI__ . '?controller=order-detail&id_order=' . $orderCore->id,
                    (new Currency((int)$orderCore->id_currency))->iso_code,
                    $this->getOrderStatus($orderCore),
                    (int)$orderCore->id_cart,
                    '',
                    floatval($orderCore->total_shipping_tax_incl),
                    $this->getOrderStatus($orderCore),
                    $date->format('Y-m-d\TH:i:sO'),
                    $this->getOrderShippingAddress($orderCore),
                    $this->getOrderBillingAddress($orderCore),
                    $this->getCartForOrder($orderCore)
                ));
            }

            try {
                $exportService->exportContact(new GrExportContactCommand(
                    $contact['email'],
                    $contact['firstname'] . ' ' . $contact['lastname'],
                    $settings,
                    $this->mapCustomFields($grCustoms, $contact,
                        $settings->isUpdateContactEnabled()),
                    $orders
                ));
            } catch (GetresponseApiException $e) {
                if ($e->getMessage() !== 'Cannot add contact that is blacklisted') {
                    throw $e;
                }
            }
        }
    }

    /**
     * @param OrderCore $order
     * @return string
     */
    private function getOrderStatus($order)
    {
        $status = (new OrderState((int)$order->getCurrentState(), $order->id_lang))->name;

        return empty($status) ? 'new' : $status;
    }

    /**
     * @param GrCustomFieldsCollection $grCustoms
     * @param array $contact
     * @param bool $useCustoms
     * @return GrCustomFieldsCollection
     * @throws PrestaShopDatabaseException
     */
    private function mapCustomFields($grCustoms, $contact, $useCustoms)
    {
        $c = array();

        /** @var GrCustomField $grCustom */
        foreach ($grCustoms as $grCustom) {
            $c[$grCustom->getName()] = $grCustom->getId();
        }

        $collection = new GrCustomFieldsCollection();

        if (false === $useCustoms) {
            return $collection;
        }

        $mappingCollection = $this->repository->getCustoms();

        foreach ($mappingCollection as $mapping) {
            if ('yes' === $mapping['active_custom'] && isset($contact[$mapping['custom_name']])) {
                $collection->add(new GrCustomField($c[$mapping['custom_name']], $mapping['custom_name'], $contact[$mapping['custom_name']]));
            }
        }

        return $collection;
    }

    /**
     * @param array $product
     * @return GrProduct
     */
    private function createGrProductObject($product)
    {
        $imagesCollection = new \GrShareCode\Product\Variant\Images\ImagesCollection();
        $categoryCollection = new GrCategoryCollection();
        $coreProduct = new Product($product['id_product']);
        $categories = $coreProduct->getCategories();

        foreach ($coreProduct->getImages(null) as $image) {
            $imagePath = (new Link())->getImageLink($coreProduct->link_rewrite, $image['id_image'], 'home_default');
            $imagesCollection->add(new \GrShareCode\Product\Variant\Images\Image(Tools::getProtocol() . $imagePath, (int)$image['position']));
        }

        foreach ($categories as $category) {
            $coreCategory = new Category($category);
            $categoryCollection->add(new GrCategory($coreCategory->getName()));
        }

        $grVariant = new GrVariant(
            (int)$product['id_product'],
            $this->normalizeToString($coreProduct->name),
            $coreProduct->getPrice(false),
            $coreProduct->getPrice(),
            $product['reference']
        );
        $grVariant->setImages($imagesCollection);
        $grVariant->setUrl((new Link())->getProductLink($coreProduct));

        return new GrProduct(
            (int)$product['id_product'],
            $this->normalizeToString($coreProduct->name),
            $grVariant,
            $categoryCollection
        );
    }

    public function createAsyncExportRequest()
    {

        $request = serialize($this->exportSettings->getSettings());
        $this->repository->insertExportRequest('export', $request);
    }

    /**
     * @param Order $order
     * @return GrProductsCollection
     */
    private function getOrderProductsCollection(Order $order)
    {
        $productsCollection = new GrProductsCollection();
        $products = $order->getProducts();

        foreach ($products as $product) {
            $productsCollection->add($this->createGrProductObject($product));
        }

        return $productsCollection;
    }

    /**
     * @param Order $order
     * @return GrAddress
     */
    private function getOrderShippingAddress(Order $order)
    {
        $address = new Address($order->id_address_delivery);
        $country = new Country($address->id_country);
        return new GrAddress(\GrShareCode\CountryCodeConverter::getCountryCodeInISO3166Alpha3($country->iso_code), $this->normalizeToString($country->name));
    }

    /**
     * @param Order $order
     * @return GrAddress
     */
    private function getOrderBillingAddress(Order $order)
    {
        $address = new Address($order->id_address_invoice);
        $country = new Country($address->id_country);
        return new GrAddress(\GrShareCode\CountryCodeConverter::getCountryCodeInISO3166Alpha3($country->iso_code), $this->normalizeToString($country->name));
    }

    /**
     * @param Order $order
     * @return GrCart
     */
    private function getCartForOrder(Order $order)
    {
        $coreCart = new Cart((int)$order->id_cart);
        $productsCollection = new \GrShareCode\Product\ProductsCollection();

        foreach ($coreCart->getProducts() as $product) {
            $productsCollection->add($this->createGrProductObject($product));
        }

        return new GrShareCode\Cart\Cart(
            $coreCart->id,
            $productsCollection,
            (new Currency((int)$coreCart->id_currency))->iso_code,
            $coreCart->getOrderTotal(false),
            $coreCart->getOrderTotal(true)
        );
    }

    /**
     * @param string $text
     * @return mixed
     */
    private function normalizeToString($text)
    {
        return is_array($text) ? reset($text) : $text;
    }
}
