<?php

use GetResponse\Account\AccountDto;
use GetResponse\Account\AccountServiceFactory;
use GetResponse\Account\AccountSettings;
use GetResponse\Account\AccountStatusFactory;
use GetResponse\Account\AccountValidator;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\GetresponseApiException;

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
        $accountStatus = AccountStatusFactory::create();
        $this->display = $accountStatus->isConnectedToGetResponse() ? 'view' : 'edit';
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
        $accountDto = AccountDto::fromRequest([
            'apiKey' => Tools::getValue('api_key'),
            'enterprisePackage' => Tools::getValue('is_enterprise'),
            'domain' => Tools::getValue('domain'),
            'accountType' => Tools::getValue('account_type')
        ]);

        $validator = new AccountValidator($accountDto);
        if (!$validator->isValid()) {
            $this->errors = $validator->getErrors();

            return;
        }

        try {
            $accountService = AccountServiceFactory::createFromAccountDto($accountDto);

            if ($accountService->isConnectionAvailable()) {

                $accountService->updateApiSettings(
                    $accountDto->getApiKey(),
                    $accountDto->getAccountTypeForSettings(),
                    $accountDto->getDomain()
                );

                $this->confirmations[] = $this->l('GetResponse account connected');

            } else {

                $msg = !$accountDto->isEnterprisePackage()
                    ? 'The API key or domain seems incorrect.'
                    : 'The API key seems incorrect.';

                $msg .= ' Please check if you typed or pasted it correctly.
                    If you recently generated a new key, please make sure you\'re using the right one';

                $this->errors[] = $this->l($msg);
            }
        } catch (GetresponseApiException $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * @return mixed
     * @throws ApiTypeException
     * @throws GetresponseApiException
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
        $fields_form = [
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
                                'value' => AccountSettings::ACCOUNT_TYPE_360_PL,
                                'label' => $this->l('GetResponse Enterprise PL')
                            ],
                            [
                                'id' => 'account_en',
                                'value' => AccountSettings::ACCOUNT_TYPE_360_US,
                                'label' => $this->l('GetResponse Enterprise COM')
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
        ];

        $helper = new HelperForm();
        $helper->submit_action = 'connectToGetResponse';
        $helper->token = Tools::getAdminTokenLite('AdminGetresponseAccount');
        $helper->fields_value = [
            'api_key' => Tools::getValue('api_key'),
            'is_enterprise' => Tools::getValue('is_enterprise'),
            'domain' => Tools::getValue('domain'),
            'account_type' => Tools::getValue('account_type'),
            'action' => 'api',
        ];

        return $helper->generateForm(array($fields_form));
    }

}
