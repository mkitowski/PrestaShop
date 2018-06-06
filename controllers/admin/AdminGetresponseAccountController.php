<?php
require_once 'AdminGetresponseController.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
/**
 * Class AdminGetresponseAccountController
 * @author Getresponse <grintegrations@getresponse.com>
 * @copyright GetResponse
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class AdminGetresponseAccountController extends AdminGetresponseController
{
    public function __construct()
    {
        parent::__construct();

        $this->meta_title = $this->l('GetResponse Integration');
        $this->identifier  = 'AdminGetresponseAccountController';

        $this->context->smarty->assign(array(
            'gr_tpl_path' => _PS_MODULE_DIR_ . 'getresponse/views/templates/admin/',
            'action_url' => $this->context->link->getAdminLink('AdminGetresponseAccount'),
            'base_url', __PS_BASE_URI__
        ));

        if (Tools::isSubmit('connectToGetResponse')) {
            $this->connectToGetResponse();
        } elseif (Tools::isSubmit('disconnectFromGetResponse')) {
            $this->disconnectFromGetResponse();
        }
    }

    public function initContent()
    {
        $settings = $this->repository->getSettings();
        $this->display = !empty($settings['api_key']) ? 'view' : 'edit';
        $this->show_form_cancel_button = false;

        parent::initContent();
    }

    /**
     * Toolbar title
     */
    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('Administration');
        $this->toolbar_title[] = $this->l('GetResponse Account');
    }

    /**
     * API key settings
     */
    public function apiView()
    {
        $api = $this->getGrAPI();
        $grAccount = new GrAccount($api, $this->repository);

        if (!empty($grAccount->getApiKey())) {
            $data = $grAccount->getInfo();

            $this->context->smarty->assign(array(
                'gr_acc_name' => $data['firstName'] . ' ' . $data['lastName'],
                'gr_acc_email' => $data['email'],
                'gr_acc_company' => $data['companyName'],
                'gr_acc_phone' => $data['phone'],
                'gr_acc_address' => $data['city'] . ' ' . $data['street'] . ' ' . $data['zipCode'],
            ));
        }

        $this->context->smarty->assign(array(
            'api_key' => $this->hideApiKey($grAccount->getApiKey()),
            'is_connected' => !empty($api) ? true : false
        ));
    }

    /**
     * render main view
     * @return mixed
     */
    public function renderView()
    {
        $settings = $this->repository->getSettings();
        $isConnected = !empty($settings['api_key']) ? true : false;

        $this->context->smarty->assign(array(
            'selected_tab' => 'api',
            'is_connected' => $isConnected,
            'active_tracking' => $settings['active_tracking']
        ));

        $this->apiView();
        return parent::renderView();
    }


    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Connect your site and GetResponse'),
                'icon' => 'icon-gears'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('API key'),
                    'name' => 'api_key',
                    'desc' =>
                        $this->l(
                            'Your API key is part of the settings of your GetResponse account.
                            Log in to GetResponse and go to'
                        ) .
                        ' <strong> ' . $this->l('My profile > Integration & API > API') . ' </strong> ' .
                        $this->l('to find the key')
                    ,
                    'empty_message' => $this->l('You need to enter API key. This field can\'t be empty.'),
                    'required' => true
                ),
                array(
                    'type'      => 'switch',
                    'label'     => $this->l('Enterprise package'),
                    'name'      => 'is_enterprise',
                    'required'  => false,
                    'class'     => 't',
                    'is_bool'   => true,
                    'values'    => array(
                        array(
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Account type'),
                    'name' => 'account_type',
                    'required' => false,
                    'values' =>  array(
                        array(
                            'id' => 'account_pl',
                            'value' => '360pl',
                            'label' => $this->l('GetResponse 360 PL')
                        ),
                        array(
                            'id' => 'account_en',
                            'value' => '360en',
                            'label' => $this->l('GetResponse 360 COM')
                        )
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Your domain'),
                    'name' => 'domain',
                    'required' => false,
                    'id' => 'domain',
                    'desc' => $this->l('Enter your domain without protocol (https://) eg: "example.com"'),
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'action',
                    'values' => 'api',
                    'default' => 'api'
                )
            ),
            'submit' => array(
                'title' => $this->l('Connect'),
                'name' => 'connectToGetResponse',
                'icon' => 'icon-getresponse-connect icon-link'
            )
        );

        //hack for setting default value of form input
        if (empty($_POST['action'])) {
            $_POST['action'] = 'api';
        }

        return parent::renderForm();
    }

    /**
     * Process Refresh Data
     * @return mixed
     */
    public function processRefreshData()
    {
        return $this->module->refreshDatas();
    }

    private function disconnectFromGetResponse()
    {
        $grAccount = new GrAccount(GrTools::getApiInstance($this->repository->getSettings()), $this->repository);
        $grAccount->updateApiSettings(null, 'gr', null);

        /** @var CacheCore $cache */
        $cache = Cache::getInstance();
        $cache->delete('GetResponse*');

        $this->confirmations[] = $this->l('GetResponse account disconnected');
    }

    private function connectToGetResponse()
    {
        $apiKey = Tools::getValue('api_key');
        $isEnterprise = (bool) Tools::getValue('is_enterprise');
        $accountType = Tools::getValue('account_type');
        $domain = Tools::getValue('domain');
        $accountType = $isEnterprise ? $accountType : 'gr';

        if (false === $this->validateConnectionFormParams($apiKey, $isEnterprise, $accountType, $domain)) {
            return;
        }

        try {
            $grAccount = new GrAccount(
                GrTools::getApiInstance(['api_key' => $apiKey, 'account_type' => $accountType, 'crypto' => $domain]),
                $this->repository
            );

            if ($grAccount->checkConnection()) {
                $grAccount->updateApiSettings($apiKey, $accountType, $domain);
                $this->confirmations[] = $this->l('GetResponse account connected');
                $grAccount->updateTracking((false === $grAccount->isTrackingAvailable()) ? 'disabled': 'no', '');
            } else {
                $msg = $accountType !== 'gr'
                    ? 'The API key or domain seems incorrect.'
                    : 'The API key seems incorrect.';
                $msg .= ' Please check if you typed or pasted it correctly.
                    If you recently generated a new key, please make sure you\'re using the right one';
                $this->errors[] = $this->l($msg);
            }
        } catch (\GrShareCode\GetresponseApiException $e) {
            $this->errors[] = $e->getMessage();
        } catch (\GrShareCode\Api\ApiTypeException $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * @param string $apiKey
     * @param bool $isEnterprise
     * @param string $accountType
     * @param string $domain
     * @return bool
     */
    private function validateConnectionFormParams($apiKey, $isEnterprise, $accountType, $domain)
    {
        if (empty($apiKey)) {
            $this->errors[] = $this->l('You need to enter API key. This field can\'t be empty.');
            return false;
        }

        if (false === $isEnterprise) {
            return true;
        }

        if (empty($accountType)) {
            $this->errors[] = $this->l('Invalid account type');
            return false;
        }

        if (empty($domain)) {
            $this->errors[] = $this->l('Domain field can not be empty');
            return false;
        }

        return true;
    }

    /**
     * Get Admin Token
     * @return string
     */
    public function getToken()
    {
        return Tools::getAdminTokenLite('AdminGetresponseAccount');
    }

    /**
     * @param string $apiKey
     *
     * @return string
     */
    private function hideApiKey($apiKey)
    {
        if (Tools::strlen($apiKey) > 0) {
            return str_repeat("*", Tools::strlen($apiKey) - 6) . Tools::substr($apiKey, -6);
        }

        return $apiKey;
    }
}
