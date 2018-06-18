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
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/GetResponseExportSettings.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/GetResponseRepository.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/GrAccount.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/GrExport.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/GrShop.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/exceptions/GrGeneralException.php');
include_once(_PS_MODULE_DIR_ . '/getresponse/classes/exceptions/GrConfigurationNotFoundException.php');

use GetResponse\Config\ConfigService as GrConfigService;
use GrShareCode\GetresponseApi;
use GrShareCode\GetresponseApiException;
use GrShareCode\Job\JobException as GrJobException;
use GrShareCode\Job\RunCommand as GrRunCommand;
use GetResponse\Hook\FormDisplay as GrFormDisplay;
use GetResponse\WebForm\WebFormRepository as GrWebFormRepository;
use GetResponse\Contact\ContactDtoFactory as GrContactDtoFactory;
use GetResponse\Account\AccountServiceFactory as GrAccountServiceFactory;
use GetResponse\Hook\NewOrder as GrNewOrderHook;
use GetResponse\Hook\NewCart as GrNewCartHook;
use GetResponse\Api\ApiFactory as GrApiFactory;
use GetResponse\Account\AccountSettings as GrAccountSettings;

class Getresponse extends Module
{
    const X_APP_ID = '2cd8a6dc-003f-4bc3-ba55-c2e4be6f7500';
    const VERSION = '16.3.0';

    /** @var GetResponseRepository */
    private $repository;

    /** @var GrAccountSettings */
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

        $this->repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());
        $this->settings = (GrAccountServiceFactory::create())->getSettings();

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
     * @return bool
     */
    public function isPluginEnabled()
    {
        $accountService = GrAccountServiceFactory::create();
        $settings = $accountService->getSettings();
        return $settings->isConnectedWithGetResponse();
    }

    /******************************************************************/
    /** Hook Methods **************************************************/
    /******************************************************************/

    /**
     * @param array $params
     */
    public function hookCart($params)
    {
        try {
            $cartHook = new GrNewCartHook($this->getGrAPI(), $this->repository, Db::getInstance());
            $cartHook->sendCart($params['cart']);
        } catch (GetresponseApiException $e) {
        } catch (PrestaShopException $e) {
        }
    }

    /**
     * @param $params
     */
    public function hookNewOrder($params)
    {
        $this->sendOrderToGr($params['order']);
    }

    /**
     * @param array $params
     */
    public function hookHookOrderConfirmation($params)
    {
        $this->sendOrderToGr($params['order']);
    }

    /**
     * @param array $params
     */
    public function hookPostUpdateOrderStatus($params)
    {
        $this->sendOrderToGr($params['order']);
    }

    /**
     * @param Order $order
     */
    private function sendOrderToGr(Order $order)
    {
        try {
            $orderHook = new GrNewOrderHook($this->getGrAPI(), $this->repository, Db::getInstance());
            $orderHook->sendOrder($order);
        } catch (GetresponseApiException $e) {
        } catch (PrestaShopException $e) {
        }
    }

    /**
     * @param array $params
     */
    public function hookCreateAccount($params)
    {
        $this->createSubscriber($params);
    }

    /**
     * @param array $params
     */
    public function createSubscriber(array $params)
    {
        try {
            $prefix = isset($params['newNewsletterContact']) ? 'newNewsletterContact' : 'newCustomer';
            $contactDto = GrContactDtoFactory::createFromForm($params[$prefix]);
            $contactHook = new \GetResponse\Hook\NewContact($this->getGrAPI(), $this->repository, Db::getInstance());
            $contactHook->sendContact($contactDto);
        } catch (GetresponseApiException $e) {
        } catch (PrestaShopDatabaseException $e) {
        }
    }

    /**
     * @return GetresponseApi
     */
    public function getGrAPI()
    {
        $accountService = GrAccountServiceFactory::create();
        return GrApiFactory::createFromSettings($accountService->getSettings());
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
        if ('yes' == $this->settings->getActiveTracking()) {
            $this->smarty->assign(array('gr_tracking_snippet' => $this->settings->getTrackingSnippet()));
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

        if (Tools::isSubmit('submitNewsletter')
            && '0' == Tools::getValue('action')
            && Validate::isEmail(Tools::getValue('email'))
            && 'yes' == $this->settings->getActiveNewsletterSubscription()
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
            'yes' == $this->settings->getActiveTracking()
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
     * @return array
     */
    public function getCronFrequency()
    {
        return array('hour' => -1, 'day' => -1, 'month' => -1, 'day_of_week' => -1);
    }

    /**
     * @param array $params
     * @return bool
     * @throws GetresponseApiException
     * @throws GrJobException
     */
    public function hookActionCronJob($params = array())
    {
        if (false === $this->settings->isConnectedWithGetResponse()) {
            return true;
        }

        $command = new GrRunCommand($this->getGrAPI(), $this->repository);
        $command->execute();
        return true;
    }
}
