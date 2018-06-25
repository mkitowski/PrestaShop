<?php

use GrShareCode\Contact\ContactService as GrContactService;
use GrShareCode\Contact\CustomFieldsCollection as GrCustomFieldsCollection;
use GrShareCode\Contact\CustomField as GrCustomField;
use GrShareCode\GetresponseApiException;
use GrShareCode\Cart\Cart as GrCart;
use GrShareCode\Product\ProductsCollection as GrProductsCollection;
use GrShareCode\Product\Category\CategoryCollection as GrCategoryCollection;
use GrShareCode\Product\Category\Category as GrCategory;
use GrShareCode\Product\Variant\Variant as GrVariant;
use GrShareCode\Product\Product as GrProduct;
use GrShareCode\Product\ProductService as GrProductService;
use GrShareCode\Cart\CartService as GrCartService;
use GrShareCode\Export\Settings\EcommerceSettings as GrEcommerceSettings;
use GrShareCode\Export\Settings\ExportSettings as GrExportSettings;
use GrShareCode\Order\OrderService as GrOrderService;
use GrShareCode\Export\ExportContactService as GrExportContactService;
use GrShareCode\Export\ExportContactCommand as GrExportContactCommand;
use GrShareCode\Export\HistoricalOrder\HistoricalOrderCollection as GrHistoricalOrderCollection;
use GrShareCode\Export\HistoricalOrder\HistoricalOrder as GrHistoricalOrder;
use GrShareCode\Address\Address as GrAddress;
use GrShareCode\Product\Variant\Images\Image as GrImage;
use GrShareCode\Product\Variant\Images\ImagesCollection as GrImagesCollection;
use GrShareCode\CountryCodeConverter as GrCountryCodeConverter;
use GrShareCode\Job\JobFactory as GrJobFactory;
use GetResponse\Helper\Shop as GrShop;
use GetResponse\Api\ApiFactory as GrApiFactory;
use GetResponse\Account\AccountServiceFactory as GrAccountServiceFactory;

class GrExport
{
    /** @var GetResponseExportSettings */
    private $exportSettings;

    /** @var GetResponseRepository */
    private $repository;

    /** @var GrCustomFieldsCollection */
    private $grCustoms;

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
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function export()
    {
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $accountService = GrAccountServiceFactory::create();
        $api = GrApiFactory::createFromSettings($accountService->getSettings());
        $contactService = new GrContactService($api);
        $productService = new GrProductService($api, $repository);
        $cartService = new GrCartService($api, $repository, $productService);
        $orderService = new GrOrderService($api, $repository, $productService);
        $exportService = new GrExportContactService($contactService, $cartService, $orderService);
        $contacts = $repository->getContacts($this->exportSettings->isNewsletter());
        $this->grCustoms = $contactService->getAllCustomFields();
        $settings = new GrExportSettings(
            $this->exportSettings->getListId(),
            $this->exportSettings->getCycleDay(),
            $this->exportSettings->isAsyncExport(),
            $this->exportSettings->isUpdateAddress(),
            new GrEcommerceSettings(
                $this->exportSettings->isExportEcommerce(),
                $repository->getGrShopId()
            )
        );

        foreach ($contacts as $contact) {
            $orders = new GrHistoricalOrderCollection();

            if ($this->exportSettings->isExportEcommerce()) {
                $customerOrders = $this->repository->getOrders($contact['id']);

                foreach ($customerOrders as $customerOrder) {
                    $orderCore = new Order($customerOrder['id_order']);
                    $date = DateTime::createFromFormat('Y-m-d H:i:s',
                        $orderCore->date_add);
                    $orders->add(new GrHistoricalOrder(
                        (string)$customerOrder['id_order'],
                        $this->getOrderProductsCollection($orderCore),
                        floatval($orderCore->total_paid_tax_excl),
                        floatval($orderCore->total_paid_tax_incl),
                        Tools::getHttpHost(true) . __PS_BASE_URI__ . '?controller=order-detail&id_order=' . $orderCore->id,
                        (new Currency((int)$orderCore->id_currency))->iso_code,
                        $this->getOrderStatus($orderCore),
                        (string)$orderCore->id_cart,
                        '',
                        floatval($orderCore->total_shipping_tax_incl),
                        $this->getOrderStatus($orderCore),
                        $date->format('Y-m-d\TH:i:sO'),
                        $this->getOrderShippingAddress($orderCore),
                        $this->getOrderBillingAddress($orderCore),
                        $this->getCartForOrder($orderCore)
                    ));
                }
            }

            try {
                $exportCommand = new GrExportContactCommand(
                    $contact['email'],
                    $contact['firstname'] . ' ' . $contact['lastname'],
                    $settings,
                    $this->mapCustomFields($contact, $settings->isUpdateContactEnabled()),
                    $orders
                );

                if ($settings->isJobSchedulerEnabled()) {
                    $this->repository->addJob(GrJobFactory::createForContactExportCommand($exportCommand));
                } else {
                    $exportService->exportContact($exportCommand);
                }
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
     * @param array $contact
     * @param bool $useCustoms
     * @return GrCustomFieldsCollection
     * @throws PrestaShopDatabaseException
     */
    private function mapCustomFields($contact, $useCustoms)
    {
        $c = array();

        /** @var GrCustomField $grCustom */
        foreach ($this->grCustoms as $grCustom) {
            $c[$grCustom->getName()] = $grCustom->getId();
        }

        $collection = new GrCustomFieldsCollection();

        if (false === $useCustoms) {
            return $collection;
        }

        $mappingCollection = $this->repository->getCustoms();

        foreach ($mappingCollection as $mapping) {
            if (!isset($c[$mapping['custom_name']])) {
                continue;
            }
            if ('yes' === $mapping['active_custom'] && isset($contact[$mapping['custom_name']])) {
                $collection->add(new GrCustomField($c[$mapping['custom_name']], $mapping['custom_name'], $contact[$mapping['custom_name']]));
            }
        }

        return $collection;
    }

    /**
     * @param $product
     * @return GrProduct
     * @throws PrestaShopException
     */
    private function createGrProductObject($product)
    {
        $imagesCollection = new GrImagesCollection();
        $categoryCollection = new GrCategoryCollection();
        $coreProduct = new Product($product['id_product']);
        $categories = $coreProduct->getCategories();

        foreach ($coreProduct->getImages(null) as $image) {
            $imagePath = (new Link())->getImageLink($coreProduct->link_rewrite, $image['id_image'], 'home_default');
            $imagesCollection->add(new GrImage(Tools::getProtocol(Tools::usingSecureMode()) . $imagePath, (int)$image['position']));
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
        //@TODO: pobrac ilosc
        $grVariant->setQuantity(100);
        $grVariant->setImages($imagesCollection);
        $grVariant->setUrl((new Link())->getProductLink($coreProduct));

        return new GrProduct(
            (int)$product['id_product'],
            $this->normalizeToString($coreProduct->name),
            $grVariant,
            $categoryCollection
        );
    }

    /**
     * @param Order $order
     * @return GrProductsCollection
     * @throws PrestaShopException
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
        return new GrAddress(
            GrCountryCodeConverter::getCountryCodeInISO3166Alpha3($country->iso_code),
            $this->normalizeToString($country->name)
        );
    }

    /**
     * @param Order $order
     * @return GrAddress
     */
    private function getOrderBillingAddress(Order $order)
    {
        $address = new Address($order->id_address_invoice);
        $country = new Country($address->id_country);
        return new GrAddress(
            GrCountryCodeConverter::getCountryCodeInISO3166Alpha3($country->iso_code),
            $this->normalizeToString($country->name)
        );
    }

    /**
     * @param Order $order
     * @return GrCart
     * @throws PrestaShopException
     */
    private function getCartForOrder(Order $order)
    {
        $coreCart = new Cart((int)$order->id_cart);
        $productsCollection = new GrProductsCollection();

        foreach ($coreCart->getProducts() as $product) {
            $productsCollection->add($this->createGrProductObject($product));
        }

        return new GrCart(
            (string)$coreCart->id,
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
