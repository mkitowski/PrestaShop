<?php
require_once 'AdminGetresponseController.php';

use GetResponse\Account\AccountServiceFactory;
use GetResponse\ContactList\ContactListServiceFactory;
use GetResponse\ContactList\SubscribeViaRegistrationDto;
use GetResponse\Helper\FlashMessages;
use GetResponse\Settings\Registration\RegistrationRepository;
use GetResponse\Settings\Registration\RegistrationSettings;
use GetResponse\Settings\Registration\RegistrationSettingsValidator;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\Api\Exception\GetresponseApiException;

class AdminGetresponseSubscribeRegistrationController extends AdminGetresponseController
{
    const UPDATE_CONTACT_ENABLED = '1';
    const UPDATE_CONTACT_DISABLED = '0';

    /**
     * @throws PrestaShopException
     * @throws ApiTypeException
     */
    public function __construct()
    {
        parent::__construct();
        $this->addJquery();
        $this->addJs(_MODULE_DIR_ . $this->module->name . '/views/js/gr-registration.js');

        $this->name = 'GRSubscribeRegistration';
        $this->context->smarty->assign(array(
            'gr_tpl_path' => _PS_MODULE_DIR_ . 'getresponse/views/templates/admin/',
            'action_url' => $this->context->link->getAdminLink('AdminGetresponseSubscribeRegistration'),
            'base_url',
            __PS_BASE_URI__
        ));
    }

    public function initContent()
    {
        $this->display = 'view';
        $this->toolbar_title[] = $this->l('GetResponse');
        $this->toolbar_title[] = $this->l('Add Contacts During Registrations');
        parent::initContent();
    }

    /**
     * @throws PrestaShopException
     */
    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['add_campaign'] = [
            'href' => (new LinkCore())->getAdminLink('AdminGetresponseAddNewContactList') . '&referer=' . $this->controller_name,
            'desc' => $this->l('Add new contact list'),
            'icon' => 'process-icon-new'
        ];

        parent::initPageHeaderToolbar();
    }

    /**
     * @return bool|ObjectModel|void
     * @throws PrestaShopException
     */
    public function postProcess()
    {
        if (Tools::isSubmit('saveSubscribeForm')) {

            $registrationSettings = RegistrationSettings::createFromPost(Tools::getAllValues());
            $validator = new RegistrationSettingsValidator($registrationSettings);

            if (!$validator->isValid()) {
                $this->errors = $validator->getErrors();
                return;
            }

            $registrationRepository = new RegistrationRepository();
            $registrationRepository->updateSettings($registrationSettings);

            FlashMessages::add(FlashMessages::TYPE_CONFIRMATION, $this->l('Settings saved'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminGetresponseSubscribeRegistration'));
        }
    }

    /**
     * render main view
     * @return mixed
     * @throws GetresponseApiException
     * @throws PrestaShopDatabaseException
     * @throws SmartyException
     * @throws PrestaShopException
     */
    public function renderView()
    {
        $accountSettings = AccountServiceFactory::create()->getAccountSettings();
        $registrationSettings = (new RegistrationRepository())->getSettings();
        $contactListService = $contactListService = ContactListServiceFactory::create();

        $this->context->smarty->assign(array(
            'is_connected' => $accountSettings->isConnectedWithGetResponse()
        ));

        $this->context->smarty->assign([
            'selected_tab' => 'subscribe_via_registration',
            'token' => $this->getToken(),
            'subscribe_via_registration_form' => $this->renderSubscribeRegistrationForm(
                $this->getCampaignsOptions(),
                $registrationSettings->getCycleDay()
            ),
            'subscribe_via_registration_list' => $this->renderCustomList(),
            'campaign_days' => json_encode($this->getCampaignDays($contactListService->getAutoresponders())),
            'cycle_day' => $registrationSettings->getCycleDay(),
        ]);


        return parent::renderView();
    }

    /**
     * Get Admin Token
     * @return string
     */
    public function getToken()
    {
        return Tools::getAdminTokenLite('AdminGetresponseSubscribeRegistration');
    }

    /**
     * Returns subscribe on register form
     * @param array $campaigns
     * @param null|int $addToCycle
     * @return mixed
     * @throws SmartyException
     */
    public function renderSubscribeRegistrationForm($campaigns = array(), $addToCycle = null)
    {
        if (is_string($addToCycle) && strlen($addToCycle) > 0) {
            $addToCycle = 'checked="checked"';
        } else {
            $addToCycle = '';
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Subscribe after Customer Register'),
                'icon' => 'icon-gears'
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->l('Add contacts to GetResponse during registration'),
                    'name' => 'subscriptionSwitch',
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
                    'type' => 'switch',
                    'label' => $this->l('Include Prestashop newsletter subscribers'),
                    'name' => 'newsletter',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'newsletter_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ],
                        [
                            'id' => 'newsletter_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        ]
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Contact list'),
                    'name' => 'campaign',
                    'required' => true,
                    'desc' =>
                        '<input type="checkbox" id="addToCycle" value="1" name="addToCycle" ' .
                        $addToCycle . '> ' . $this->l('Add to autoresponder cycle'),
                    'options' => [
                        'query' => $campaigns,
                        'id' => 'id_option',
                        'name' => 'name'
                    ]
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Autoresponder Day'),
                    'name' => 'cycledays',
                    'options' => [
                        'query' => [],
                        'id' => 'id_option',
                        'name' => 'name'
                    ]
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Update contact info'),
                    'name' => 'contactInfo',
                    'class' => 't',
                    'is_bool' => true,
                    'desc' =>
                        $this->l('
                            Select this option if you want to overwrite contact details 
                            that already exist in your GetResponse database.
                        ') .
                        '<br>' .
                        $this->l('Clear this option to keep existing data.'),
                    'values' => [
                        [
                            'id' => 'contact_on',
                            'value' => self::UPDATE_CONTACT_ENABLED,
                            'label' => $this->l('Enabled')
                        ],
                        [
                            'id' => 'contact_off',
                            'value' => self::UPDATE_CONTACT_DISABLED,
                            'label' => $this->l('Disabled')
                        ]
                    ]
                ]
            ],
            'submit' =>
                [
                    'title' => $this->l('Save'),
                    'name' => 'saveSubscribeForm',
                    'icon' => 'process-icon-save'
                ]
        ];

        return parent::renderForm();
    }

    /**
     * Renders custom list
     * @return mixed
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function renderList()
    {
        return $this->renderCustomList();
    }

    /**
     * Assigns values to forms
     * @param $obj
     * @return array
     */
    public function getFieldsValue($obj)
    {
        $settings = (new RegistrationRepository())->getSettings();
        return [
            'subscriptionSwitch' => $settings->isActive() ? 1 : 0,
            'campaign' => $settings->getListId(),
            'cycledays' => $settings->getCycleDay(),
            'contactInfo' => $settings->isUpdateContactEnabled() ? 1 : 0,
            'newsletter' => $settings->isNewsletterActive() ? 1 : 0
        ];
    }
}
