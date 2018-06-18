<?php
/**
 * This module integrate GetResponse and PrestaShop Allows subscribe via checkout page and export your contacts.
 *
 *  @author Getresponse <grintegrations@getresponse.com>
 *  @copyright GetResponse
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(_PS_MODULE_DIR_ . '/getresponse/vendor/autoload.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/DbConnection.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/GrApiException.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/GetResponseAPI3.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/GetResponseExportSettings.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/GetResponseRepository.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/GrAccount.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/GrApi.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/GrExport.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/GrShop.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/GrApiFactory.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/GrEcommerce.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/exceptions/GrGeneralException.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/exceptions/GrConfigurationNotFoundException.php');

use GetResponse\Config\ConfigService as GrConfigService;
use GetResponse\Settings\SettingsFactory as GrSettingsFactory;
use GetResponse\Settings\SettingsServiceFactory;
use GrShareCode\Cart\AddCartCommand as GrAddCartCommand;
use GrShareCode\Cart\Cart as GrCart;
use GrShareCode\Cart\CartService as GrCartService;
use GrShareCode\Contact\AddContactCommand as GrAddContactCommand;
use GrShareCode\Contact\ContactService as GrContactService;
use GrShareCode\Contact\CustomField as GrCustomField;
use GrShareCode\Contact\CustomFieldsCollection as GrCustomFieldsCollection;
use GrShareCode\GetresponseApi;
use GrShareCode\GetresponseApiException;
use GrShareCode\Job\JobException as GrJobException;
use GrShareCode\Job\RunCommand as GrRunCommand;
use GrShareCode\Product\Category\Category as GrCategory;
use GrShareCode\Product\Category\CategoryCollection as GrCategoryCollection;
use GrShareCode\Product\Product as GrProduct;
use GrShareCode\Product\ProductsCollection as GrProductsCollection;
use GrShareCode\Product\ProductService as GrProductService;
use GrShareCode\Product\Variant\Images\Image as GrImage;
use GrShareCode\Product\Variant\Images\ImagesCollection as GrImagesCollection;
use GrShareCode\Product\Variant\Variant as GrVariant;
use GetResponse\Hook\FormDisplay as GrFormDisplay;
use GetResponse\WebForm\WebFormRepository as GrWebFormRepository;
use \GetResponse\Contact\ContactDtoFactory as GrContactDtoFactory;

class Getresponse extends Module
{
    const X_APP_ID = '2cd8a6dc-003f-4bc3-ba55-c2e4be6f7500';
    const VERSION = '16.3.0';

    /**
     * @deprecated
     * @var DbConnection
     */
    private $db;

    /** @var GetResponseRepository */
    private $repository;

    /**
     * @deprecated
     * @var GrApi
     */
    private $api;

    /** @var array */
    private $settings;

    public function __construct()
    {
        $this->name = 'getresponse';
        $this->tab = 'emailing';
        $this->version = self::VERSION;
        $this->author = 'GetResponse';
        $this->need_instance = 0;
        $this->module_key = '7e6dc54b34af57062a5e822bd9b8d5ba';
        $this->ps_versions_compliancy = array('min' => '1.5.6.2', 'max' => _PS_VERSION_);
        $this->displayName = $this->l('GetResponse');
        $this->description = $this->l(GrConfigService::MODULE_DESCRIPTION);
        $this->confirmUninstall = $this->l(GrConfigService::CONFIRM_UNINSTALL);

        parent::__construct();

        $this->db = new DbConnection(Db::getInstance(), GrShop::getUserShopId());
        $this->repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());

        if (version_compare(_PS_VERSION_, '1.5') === -1) {
            $this->context->smarty->assign(array('flash_message' => array(
                'message' => $this->l('Unsupported Prestashop version'),
                'status' => 'danger'
            )));
        }

        if (!function_exists('curl_init')) {
            $this->context->smarty->assign(array('flash_message' => array(
                'message' => $this->l('Curl library not found'),
                'status' => 'danger'
            )));
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCss($this->_path . 'views/css/tab.css');
    }

    /******************************************************************/
    /** Install Methods ***********************************************/
    /******************************************************************/

    /**
     * @return bool
     */
    public function installTab()
    {
        new TabCore();
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'Getresponse';
        $tab->name = array();
        $tab->id_parent = substr(_PS_VERSION_, 0, 3) === '1.6' ? 0 : (int) Tab::getIdFromClassName('AdminAdmin');
        $tab->module = $this->name;
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'GetResponse';
        }
        $tab->add();
        $this->createSubTabs($tab->id);

        return true;
    }

    /**
     * @param int $tabId
     * @return bool
     */
    public function createSubTabs($tabId)
    {
        $langs = Language::getLanguages();
        foreach (GrConfigService::BACKOFFICE_TABS as $tab) {
            $newtab = new Tab();
            $newtab->class_name = $tab['class_name'];
            $newtab->id_parent = $tabId;
            $newtab->module = $this->name;
            $newtab->position = 0;
            foreach ($langs as $l) {
                $newtab->name[$l['id_lang']] = $this->l($tab['name']);
            }
            $newtab->add();
        }
        return true;
    }

    /**
     * @return bool
     */
    public function install()
    {
        if (!parent::install() ||!$this->installTab()) {
            return false;
        }

        foreach (GrConfigService::USED_HOOKS as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }

        //Update Version Number
        if (!Configuration::updateValue('GR_VERSION', $this->version)) {
            return false;
        }

        $this->repository->prepareDatabase();
        return true;
    }

    /******************************************************************/
    /** Uninstall Methods *********************************************/
    /******************************************************************/

    public function uninstallTab()
    {
        $result = true;
        foreach (GrConfigService::INSTALLED_CLASSES as $class) {
            $idTab = (int) Tab::getIdFromClassName($class);
            if (false === $idTab) {
                return false;
            }
            $tab = new Tab($idTab);
            $result = $tab->delete() && $result;
        }

        return $result;
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminGetresponseAccount'));
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        if (!parent::uninstall() ||!$this->uninstallTab()) {
            return false;
        }

        foreach (GrConfigService::USED_HOOKS as $hook) {
            if (!$this->unregisterHook($hook)) {
                return false;
            }
        }

        //Delete Version Entry
        if (!Configuration::deleteByName('GR_VERSION')) {
            return false;
        }

        $this->repository->clearDatabase();
        return true;
    }

    /**
     * @deprecated
     * @return GrApi
     * @throws GrConfigurationNotFoundException
     */
    private function getApi()
    {
        if (empty($this->api)) {
            $settings = $this->getSettings();
            $this->api = new GrApi($settings['api_key'], $settings['account_type'], $settings['crypto']);
        }

        return $this->api;
    }

    /**
     * @return array
     * @throws GrConfigurationNotFoundException
     */
    private function getSettings()
    {
        if (empty($this->settings)) {
            $this->settings = $this->repository->getSettings();
        }

        if (empty($this->settings['api_key'])) {
            throw new GrConfigurationNotFoundException();
        }

        return $this->settings;
    }

    /**
     * @return bool
     */
    public function isPluginEnabled()
    {
        try {
            $this->getSettings();
        } catch (GrConfigurationNotFoundException $e) {
            return false;
        }
        return true;
    }

    /******************************************************************/
    /** Hook Methods **************************************************/
    /******************************************************************/

    /**
     * @param array $params
     */
    public function hookCart($params)
    {
        $grIdShop = $this->repository->getGrShopId();

        if (empty($grIdShop)) {
            return; // E-commerce is disabled
        }

        /** @var CartCore $cart */
        $cart = $params['cart'];
        if (empty($cart) || 0 === (int)$cart->id_customer) {
            return;
        }

        $customer = new Customer($cart->id_customer);
        $settings = GrSettingsFactory::fromDb($this->getSettings());

        $api = $this->getGrAPI();
        $productService = new GrProductService($api, $this->repository);
        $cartService = new GrCartService($api, $this->repository,$productService);
        $productsCollection = new GrProductsCollection();

        foreach ($cart->getProducts() as $product) {
            $productsCollection->add($this->createGrProductObject($product));
        }

        $grCart = new GrCart(
            $cart->id,
            $productsCollection,
            (new Currency((int)$cart->id_currency))->iso_code,
            $cart->getOrderTotal(false),
            $cart->getOrderTotal(true)
        );

        $cartService->sendCart(
            new GrAddCartCommand($grCart, $customer->email, $settings->getCampaignId())
        );
    }


    /**
     * @todo move method to external service (used in GrExport)
     * @param string $text
     * @return mixed
     */
    private function normalizeToString($text)
    {
        return is_array($text) ? reset($text) : $text;
    }

    /**
     * @todo move method to external servie (used in GrExport)
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
     * @param array $params
     */
    public function hookNewOrder($params)
    {
        if ($this->isPluginEnabled()) {
            $this->addSubscriberForOrder($params);
            $this->convertCartToOrder($params);
        }
    }

    /**
     * @param array $params
     */
    public function hookHookOrderConfirmation($params)
    {
        $this->convertCartToOrder($params);
    }

    /**
     * @param array $params
     */
    public function hookPostUpdateOrderStatus($params)
    {
        $grIdShop = $this->repository->getGrShopId();
        if (empty($grIdShop)) {
            return; // E-commerce is disabled
        }

        if (isset($params['id_order']) && !empty($params['id_order'])) {
            $params['order'] = new Order($params['id_order']);
            $this->convertCartToOrder($params);
        }
    }

    /**
     * @param array $params
     */
    private function convertCartToOrder($params)
    {
        /** @var OrderCore $order */
        $order = $params['order'];
        $grIdShop = $this->repository->getGrShopId();

        if (empty($grIdShop) || empty($order) || 0 === (int)$order->id_customer) {
            return;
        }

        /** @var CustomerCore $customer */
        $customer = new Customer($order->id_customer);
        $settings = $this->repository->getSettings();
        $ecommerce = new GrEcommerce($this->db);
        $grIdContact = $ecommerce->getSubscriberId($customer->email, $settings['campaign_id'], true);

        if (empty($grIdContact)) {
            return;
        }

        $idOrder = (isset($order->id_order) && !empty($order->id_order)) ? $order->id_order : $order->id;
        $grOrder = $ecommerce->createOrderObject($params, $grIdContact, $grIdShop);
        $ecommerce->sendOrderDataToGR($grIdShop, $grOrder, $idOrder);
    }

    /**
     * @param array $params
     */
    public function hookCreateAccount($params)
    {
        if ($this->isPluginEnabled()) {
            $this->createSubscriber($params);
        }
    }

    /**
     * @param array $params
     */
    public function createSubscriber(array $params)
    {
        try {
            $settings = GrSettingsFactory::fromDb($this->getSettings());

            if ($settings->getActiveSubscription() == 'yes' && !empty($settings->getCampaignId())) {
                $prefix = isset($params['newNewsletterContact']) ? 'newNewsletterContact' : 'newCustomer';
                $contactDto = GrContactDtoFactory::createFromForm($params[$prefix]);

                if (true === $contactDto->getNewsletter()) {
                    $addContact = new GrAddContactCommand(
                        $contactDto->getEmail(),
                        $contactDto->getName(),
                        $settings->getCampaignId(),
                        $settings->getCycleDay(),
                        $this->mapCustomFields(
                            $contactDto->getCustomFields(),
                            $settings->getUpdateAddress() == 'yes'
                        )
                    );

                    $contactService = new GrContactService($this->getGrAPI());
                    $contactService->addContact($addContact);
                }
            }
        } catch (PrestaShopDatabaseException $e) {
        } catch (GrConfigurationNotFoundException $e) {
        } catch (GetresponseApiException $e) {
        }
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
     * @return GetresponseApi
     */
    public function getGrAPI()
    {
        $settingsService = SettingsServiceFactory::create();
        return GrApiFactory::createFromSettings($settingsService->getSettings());
    }

    /**
     * @param array $params
     *
     * @throws Exception
     */
    public function addSubscriberForOrder($params)
    {
        $customerPostData = $params['customer'];

        //update_contact
        $contact = $this->db->getContactByEmail($customerPostData->email);
        $customs = $this->getApi()->mapCustoms((array) $contact, $_POST, $this->db->getCustoms(), 'order');

        // automation
        if (!empty($params['order']->product_list)) {
            $categories = array();
            foreach ($params['order']->product_list as $products) {
                $tempCategories = Product::getProductCategories($products['id_product']);
                foreach ($tempCategories as $tmp) {
                    $categories[$tmp] = $tmp;
                }
            }

            $automations = $this->db->getAutomationSettings(true);

            if (!empty($automations)) {

                $automationRulesApplied = false;

                foreach ($automations as $automation) {

                    if (in_array($automation['category_id'], $categories)) {
                        // do automation
                        if ($automation['action'] == 'move') {

                            $this->getApi()->moveContactToGr(
                                $automation['campaign_id'],
                                $customerPostData->firstname,
                                $customerPostData->lastname,
                                $customerPostData->email,
                                $customs,
                                $automation['cycle_day']
                            );

                        } elseif ($automation['action'] == 'copy') {

                            $this->getApi()->addContact(
                                $automation['campaign_id'],
                                $customerPostData->firstname,
                                $customerPostData->lastname,
                                $customerPostData->email,
                                $automation['cycle_day'],
                                $customs
                            );
                            
                        }
                        $automationRulesApplied = true;
                    }
                }

                if (!$automationRulesApplied) {
                    $this->addContact($customerPostData, $customs);
                }
                return; //return so we do not hit standard case
            }
        }

        // standard case
        $this->addContact($customerPostData, $customs);
    }

    /**
     * @return string
     */
    public function hookDisplayRightColumn()
    {
        return $this->displayWebForm('right');
    }

    /**
     * @return string
     */
    public function hookDisplayLeftColumn()
    {
        return $this->displayWebForm('left');
    }

    /**
     * @return string
     */
    public function hookDisplayHeader()
    {
        $settings = $this->repository->getSettings();

        if (isset($settings['active_tracking']) && $settings['active_tracking'] == 'yes') {
            $this->smarty->assign(array('gr_tracking_snippet' => $settings['tracking_snippet']));
            return $this->display(__FILE__, 'views/templates/admin/common/tracking_snippet.tpl');
        }

        return '';
    }

    /**
     * @return string
     */
    public function hookDisplayTop()
    {
        return $this->displayWebForm('top');
    }

    /**
     * @return string
     */
    public function hookDisplayFooter()
    {
        $email = false;
        $settings = $this->repository->getSettings();

        if (Tools::isSubmit('submitNewsletter')
            && '0' == Tools::getValue('action')
            && Validate::isEmail(Tools::getValue('email'))
            && isset($settings['active_newsletter_subscription'])
            && $settings['active_newsletter_subscription'] == 'yes'
        ) {
            $client = new stdClass();
            $client->newsletter = 1;
            $client->firstname = 'Friend';
            $client->lastname = '';
            $client->email = Tools::getValue('email');

            $data = array();
            $data['newNewsletterContact'] = $client;

            $this->createSubscriber($data);
        }

        if (isset($this->context->customer) && !empty($this->context->customer->email) &&
            isset($settings['active_tracking']) && $settings['active_tracking'] == 'yes'
        ) {
            $email = $this->context->customer->email;
        }

        return $this->displayWebForm('footer') . $this->displayMailTracker($email);
    }

    /**
     * @return string
     */
    public function hookDisplayHome()
    {
        return $this->displayWebForm('home');
    }

    /**
     * @param string $position
     * @return mixed
     */
    private function displayWebForm($position)
    {
        $formDisplay = new GrFormDisplay(new GrWebFormRepository(Db::getInstance(), GrShop::getUserShopId()));
        $assignData = $formDisplay->displayWebForm($position);

        if (!empty($assignData)) {
            $this->smarty->assign($assignData);
            return $this->display(__FILE__, 'views/templates/admin/common/webform.tpl');
        }

        return '';
    }

    /**
     * @param string $email
     * @return mixed
     */
    private function displayMailTracker($email)
    {
        if (!empty($email)) {
            $this->smarty->assign(array('tracking_email' => $email));
            return $this->display(__FILE__, 'views/templates/admin/common/tracking_mail.tpl');
        }

        return '';
    }

    /**
     * @param object $contact
     * @param array $customs
     *
     * @throws Exception
     */
    private function addContact($contact, $customs)
    {
        $settings = $this->getSettings();
        if (isset($contact->newsletter) && $contact->newsletter == 1) {
            $this->getApi()->addContact(
                $settings['campaign_id'],
                $contact->firstname,
                $contact->lastname,
                $contact->email,
                $settings['cycle_day'],
                $customs
            );
        }
    }

    /**
     * @return array
     */
    public function getCronFrequency()
    {
        return array('hour' => -1, 'day' => -1, 'month' => -1, 'day_of_week' => -1);
    }

    /**
     * @param array $params
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws GetresponseApiException
     * @throws GrJobException
     */
    public function hookActionCronJob($params = array())
    {
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $dbSettings = $repository->getSettings();

        if (empty($dbSettings['api_key'])) {
            return true;
        }

        $settingsService = SettingsServiceFactory::create();
        $api = GrApiFactory::createFromSettings($settingsService->getSettings());

        $command = new GrRunCommand($api, $repository);
        $command->execute();
        return true;
    }
}
