<?php

use GetResponse\Account\AccountServiceFactory;
use GetResponse\Settings\Settings;

require_once 'AdminGetresponseController.php';

class AdminGetresponseAccountController extends AdminGetresponseController
{

    public function __construct()
    {
        parent::__construct();

        $this->meta_title = $this->l('GetResponse Integration');
        $this->identifier = 'AdminGetresponseAccountController';

        $this->context->smarty->assign(array(
            'gr_tpl_path' => _PS_MODULE_DIR_ . 'getresponse/views/templates/admin/',
            'action_url' => $this->context->link->getAdminLink('AdminGetresponseAccount'),
            'base_url',
            __PS_BASE_URI__
        ));
    }

    public function initContent()
    {
        $accountService = AccountServiceFactory::create();
        $this->display = $accountService->isConnectedToGetResponse() ? 'view' : 'edit';
        $this->show_form_cancel_button = false;

        parent::initContent();
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('Administration');
        $this->toolbar_title[] = $this->l('GetResponse Account');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('connectToGetResponse')) {
            $this->connectToGetResponse();
        } elseif (Tools::isSubmit('disconnectFromGetResponse')) {
            $accountService = AccountServiceFactory::create();
            $accountService->disconnectFromGetResponse();
            $this->confirmations[] = $this->l('GetResponse account disconnected');
        }
        parent::postProcess();
    }

    private function connectToGetResponse()
    {
        $apiKey = Tools::getValue('api_key');
        $isEnterprise = Tools::getValue('is_enterprise');
        $domain = Tools::getValue('domain');
        $accountType = $isEnterprise === '1' ? Tools::getValue('account_type') : Settings::ACCOUNT_TYPE_GR;

        if (false === $this->validateConnectionFormParams($apiKey, $isEnterprise, $accountType, $domain)) {
            return;
        }

        try {
            $accountService = AccountServiceFactory::createWithSettings([
                'api_key' => $apiKey,
                'account_type' => $accountType,
                'domain' => $domain
            ]);

            if ($accountService->isConnectionAvailable()) {

                $accountService->updateApiSettings($apiKey, $accountType, $domain);
                $this->confirmations[] = $this->l('GetResponse account connected');

            } else {

                $msg = $accountType !== Settings::ACCOUNT_TYPE_GR
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

        if (false === (bool)$isEnterprise) {
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
     * @return string
     */
    public function renderView()
    {
        $accountService = AccountServiceFactory::create();

        if ($accountService->isConnectedToGetResponse()) {

            $accountDetails = $accountService->getAccountDetails();

            $this->context->smarty->assign([
                'gr_acc_name' => $accountDetails->getFullName(),
                'gr_acc_email' => $accountDetails->getEmail(),
                'gr_acc_company' => $accountDetails->getCompanyName(),
                'gr_acc_phone' => $accountDetails->getPhone(),
                'gr_acc_address' => $accountDetails->getFullAddress(),
            ]);
        }

        $settings = $accountService->getSettings();

        $this->context->smarty->assign([
            'selected_tab' => 'api',
            'is_connected' => $accountService->isConnectedToGetResponse(),
            'active_tracking' => $settings->getActiveTracking(),
            'api_key' => $settings->getHiddenApiKey()
        ]);

        return parent::renderView();
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => [
                'legend' => [
                    'title' => $this->l('Connect your site and GetResponse'),
                    'icon' => 'icon-gears'
                ],
                'input' => [
                    [
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
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enterprise package'),
                        'name' => 'is_enterprise',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            ]
                        ],
                    ],
                    [
                        'type' => 'radio',
                        'label' => $this->l('Account type'),
                        'name' => 'account_type',
                        'required' => false,
                        'values' => [
                            [
                                'id' => 'account_pl',
                                'value' => '360pl',
                                'label' => $this->l('GetResponse 360 PL')
                            ],
                            [
                                'id' => 'account_en',
                                'value' => '360en',
                                'label' => $this->l('GetResponse 360 COM')
                            ]
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Your domain'),
                        'name' => 'domain',
                        'required' => false,
                        'id' => 'domain',
                        'desc' => $this->l('Enter your domain without protocol (https://) eg: "example.com"'),
                    ],
                    [
                        'type' => 'hidden',
                        'name' => 'action',
                        'values' => 'api',
                        'default' => 'api'
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Connect'),
                    'name' => 'connectToGetResponse',
                    'icon' => 'icon-getresponse-connect icon-link'
                ]
            ]
        );

        $helper = new HelperForm();
        $helper->submit_action = 'connectToGetResponse';
        $helper->token = Tools::getAdminTokenLite('AdminGetresponseAccount');
        $helper->fields_value = [
            'api_key' => Tools::getValue('api_key'),
            'is_enterprise' => Tools::getValue('is_enterprise'),
            'account_type' => Tools::getValue('domain'),
            'domain' => Tools::getValue('account_type')
        ];

        return $helper->generateForm(array($fields_form));
    }

}
