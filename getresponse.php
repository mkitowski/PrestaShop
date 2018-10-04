<?php
/**
 * This module integrate GetResponse and PrestaShop Allows subscribe via checkout page and export your contacts.
 *
 * @author Getresponse <grintegrations@getresponse.com>
 * @copyright GetResponse
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once _PS_MODULE_DIR_ . '/getresponse/vendor/autoload.php';
include_once _PS_MODULE_DIR_ . '/getresponse/classes/GrApiException.php';
include_once _PS_MODULE_DIR_ . '/getresponse/classes/GetResponseRepository.php';
include_once _PS_MODULE_DIR_ . '/getresponse/classes/GetResponseNotConnectedException.php';

class Getresponse extends Module
{
    const X_APP_ID = '2cd8a6dc-003f-4bc3-ba55-c2e4be6f7500';
    const VERSION = '16.3.0';

    /** @var GetResponseRepository */
    private $repository;

    /** @var GetResponse\Account\AccountSettings */
    private $settings;

    /** @var bool */
    private $isConnectedToGetResponse = true;

    public function __construct()
    {
        $this->name = 'getresponse';
        $this->tab = 'emailing';
        $this->version = self::VERSION;
        $this->author = 'GetResponse';
        $this->need_instance = 0;
        $this->module_key = '7e6dc54b34af57062a5e822bd9b8d5ba';
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        $this->displayName = $this->l('GetResponse');
        $this->description = $this->l(GetResponse\Config\ConfigService::MODULE_DESCRIPTION);
        $this->confirmUninstall = $this->l(GetResponse\Config\ConfigService::CONFIRM_UNINSTALL);

        parent::__construct();

        $this->repository = new GetResponseRepository(Db::getInstance(), GetResponse\Helper\Shop::getUserShopId());


        if (!function_exists('curl_init')) {
            $this->context->smarty->assign([
                'flash_message' => [
                    'message' => $this->l('Curl library not found'),
                    'status' => 'danger'
                ]
            ]);
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (isset($this->context->controller->module)
            && $this->context->controller->module->name === $this->name
            && $confirmations = GetResponse\Helper\FlashMessages::getConfirmations()) {

            $this->context->smarty->assign('conf', $confirmations[0]);
        }

        $this->context->controller->addCss($this->_path . 'views/css/tab.css');
    }

    /**
     * @return bool
     */
    public function install()
    {
        if (!parent::install() || !$this->installTab()) {
            return false;
        }

        foreach (GetResponse\Config\ConfigService::USED_HOOKS as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }

        // Update Version Number
        if (!Configuration::updateValue('GR_VERSION', $this->version)) {
            return false;
        }

        $this->repository->prepareDatabase();

        return true;
    }

    /**
     * @return bool
     */
    public function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'Getresponse';
        $tab->name = array();
        $tab->id_parent = strpos(_PS_VERSION_, '1.6') === 0 ? 0 : (int)Tab::getIdFromClassName('AdminAdmin');
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
        foreach (GetResponse\Config\ConfigService::BACKOFFICE_TABS as $tab) {
            $newtab = new Tab();
            $newtab->class_name = $tab['class_name'];
            $newtab->id_parent = isset($tab['parent']) ? $tab['parent'] : $tabId;
            $newtab->module = $this->name;
            $newtab->position = 0;
            foreach ($langs as $l) {
                $newtab->name[$l['id_lang']] = $this->l($tab['name']);
            }
            $newtab->add();
        }

        return true;
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
        if (!parent::uninstall() || !$this->uninstallTab()) {
            return false;
        }

        foreach (GetResponse\Config\ConfigService::USED_HOOKS as $hook) {
            if (!$this->unregisterHook($hook)) {
                return false;
            }
        }

        if (!Configuration::deleteByName('GR_VERSION')) {
            return false;
        }

        $this->repository->clearDatabase();

        return true;
    }

    /**
     * @return bool
     */
    public function uninstallTab()
    {
        $result = true;
        foreach (GetResponse\Config\ConfigService::INSTALLED_CLASSES as $class) {
            $idTab = (int)Tab::getIdFromClassName($class);
            if (false === $idTab) {
                return false;
            }
            $tab = new Tab($idTab);
            $result = $tab->delete() && $result;
        }

        return $result;
    }

    /**
     * @param $params
     * @throws GrShareCode\Api\ApiTypeException
     * @throws PrestaShopDatabaseException
     */
    public function hookCart($params)
    {
        try {
            $accountSettings = $this->getSettings();
            $cartHook = new GetResponse\Hook\NewCart();
            $cartHook->sendCart($params['cart'], $accountSettings);
        } catch (GetResponseNotConnectedException $e) {
        } catch (GrShareCode\GetresponseApiException $e) {
        } catch (PrestaShopException $e) {
        }
    }

    /**
     * @return GetResponse\Account\AccountSettings|null
     * @throws GrShareCode\Api\ApiTypeException
     * @throws GetResponseNotConnectedException
     * @throws PrestaShopDatabaseException
     */
    private function getSettings()
    {
        if (!$this->isConnectedToGetResponse) {
            throw new GetResponseNotConnectedException('GetResponse account is not connected.');
        }

        if (!$this->settings) {

            $settings = new GetResponse\Account\AccountSettingsRepository(Db::getInstance(), GetResponse\Helper\Shop::getUserShopId());
            $settings->getSettings();
            if (!GetResponse\Account\AccountStatusFactory::create()->isConnectedToGetResponse()) {
                $this->isConnectedToGetResponse = false;
                throw new GetResponseNotConnectedException('GetResponse account is not connected.');
            }

            $this->settings = GetResponse\Account\AccountServiceFactory::create()->getSettings();
        }

        return $this->settings;
    }

    /**
     * @param $params
     * @throws GrShareCode\Api\ApiTypeException
     */
    public function hookNewOrder($params)
    {
        $this->sendOrderToGr($params['order']);
    }

    /**
     * @param Order $order
     * @throws GrShareCode\Api\ApiTypeException
     */
    private function sendOrderToGr(Order $order)
    {
        try {
            $accountSettings = $this->getSettings();
            $orderHook = new GetResponse\Hook\NewOrder();
            $orderHook->sendOrder($order, $accountSettings);
        } catch (GetResponseNotConnectedException $e) {
        } catch (GrShareCode\GetresponseApiException $e) {
        } catch (PrestaShopException $e) {
        }
    }

    /**
     * @param array $params
     * @throws GrShareCode\Api\ApiTypeException
     */
    public function hookHookOrderConfirmation($params)
    {
        $this->sendOrderToGr($params['order']);
    }

    /**
     * @param array $params
     * @throws GrShareCode\Api\ApiTypeException
     */
    public function hookPostUpdateOrderStatus($params)
    {
        if (isset($params['order'])) {
            $this->sendOrderToGr($params['order']);
        }
    }

    /**
     * @param array $params
     * @throws GrShareCode\Api\ApiTypeException
     */
    public function hookCreateAccount($params)
    {
        $this->createSubscriber($params['newCustomer'], false);
    }

    /**
     * @param Customer $contact
     * @param bool $fromNewsletter
     */
    public function createSubscriber($contact, $fromNewsletter = false)
    {
        try {
            $accountSettings = $this->getSettings();

            if (!$this->getSettings()->canSubscriberBeSend() || 1 != $contact->newsletter) {
                return;
            }

            $addContactSettings = GetResponse\Contact\AddContactSettings::createFromAccountSettings($accountSettings);

            $contactService = GetResponse\Contact\ContactServiceFactory::createFromSettings($accountSettings);
            $contactService->addContact($contact, $addContactSettings, $fromNewsletter);

        } catch (GetResponseNotConnectedException $e) {
        } catch (GrShareCode\GetresponseApiException $e) {
        } catch (PrestaShopException $e) {
        }

    }

    /**
     * @return string
     * @throws GrShareCode\Api\ApiTypeException
     */
    public function hookDisplayRightColumn()
    {
        return $this->displayWebForm('right');
    }

    /**
     * @param string $position
     * @return string
     * @throws GrShareCode\Api\ApiTypeException
     */
    private function displayWebForm($position)
    {
        try {
            $formDisplay = new GetResponse\Hook\FormDisplay(
                GetResponse\WebForm\WebFormServiceFactory::createFromSettings($this->getSettings())
            );

            $assignData = $formDisplay->displayWebForm($position);

        } catch (GetResponseNotConnectedException $e) {
        }


        if (!empty($assignData)) {
            $this->smarty->assign($assignData);

            return $this->display(__FILE__, 'views/templates/admin/common/webform.tpl');
        }

        return '';
    }

    /**
     * @return string
     * @throws GrShareCode\Api\ApiTypeException
     */
    public function hookDisplayLeftColumn()
    {
        return $this->displayWebForm('left');
    }

    /**
     * @return string
     * @throws GrShareCode\Api\ApiTypeException
     */
    public function hookDisplayHeader()
    {
        try {
            $settings = $this->getSettings();

            if ($settings->isTrackingActive()) {

                $this->smarty->assign(['gr_tracking_snippet' => $settings->getTrackingSnippet()]);

                return $this->display(__FILE__, 'views/templates/admin/common/tracking_snippet.tpl');
            }

        } catch (GetResponseNotConnectedException $e) {
        }

        return '';
    }

    /**
     * @return string
     * @throws GrShareCode\Api\ApiTypeException
     */
    public function hookDisplayTop()
    {
        return $this->displayWebForm('top');
    }

    /**
     * @return string
     * @throws GrShareCode\Api\ApiTypeException
     * @throws GetResponseNotConnectedException
     */
    public function hookDisplayFooter()
    {
        try {
            $settings = $this->getSettings();
        } catch (GetResponseNotConnectedException $e) {
            return '';
        }

        $email = false;

        $this->createNewsletterSubscriber();

        if (isset($this->context->customer)
            && !empty($this->context->customer->email)
            && $settings->isTrackingActive()
        ) {
            $email = $this->context->customer->email;
        }

        return $this->displayWebForm('footer') . $this->displayMailTracker($email);
    }

    /**
     * @throws GrShareCode\Api\ApiTypeException
     * @throws GetResponseNotConnectedException
     */
    private function createNewsletterSubscriber()
    {
        if (Tools::isSubmit('submitNewsletter')
            && '0' == Tools::getValue('action')
            && Validate::isEmail(Tools::getValue('email'))
            && $this->getSettings()->isNewsletterSubscriptionOn()
        ) {

            $contact = new \Customer();
            $contact->newsletter = 1;
            $contact->firstname = 'Friend';
            $contact->lastname = '';
            $contact->email = Tools::getValue('email');

            $this->createSubscriber($contact, true);
        }
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
     * @return string
     * @throws GrShareCode\Api\ApiTypeException
     */
    public function hookDisplayHome()
    {
        return $this->displayWebForm('home');
    }

}
