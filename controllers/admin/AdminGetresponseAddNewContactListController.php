<?php
require_once 'AdminGetresponseController.php';

use GetResponse\ContactList\ContactListService;
use GetResponse\ContactList\ContactListServiceFactory;
use GetResponse\ContactList\SubscribeViaRegistrationDto;
use GetResponse\ContactList\SubscribeViaRegistrationValidator;
use GrShareCode\ContactList\ContactList;
use GrShareCode\ContactList\ContactListCollection;
use GrShareCode\ContactList\FromFields;
use GrShareCode\ContactList\SubscriptionConfirmation\SubscriptionConfirmationBody;
use GrShareCode\ContactList\SubscriptionConfirmation\SubscriptionConfirmationSubject;
use GrShareCode\ContactList\SubscriptionConfirmation\SubscriptionConfirmationSubjectCollection;
use GrShareCode\GetresponseApiException;

class AdminGetresponseAddNewContactListController extends AdminGetresponseController
{
    public $name = 'GRSubscribeRegistration';

    /** @var ContactListService */
    private $contactListService;

    public function __construct()
    {
        parent::__construct();
        $this->addJquery();
        $this->addJs(_MODULE_DIR_ . $this->module->name . '/views/js/gr-registration.js');

        $this->context->smarty->assign(array(
            'gr_tpl_path' => _PS_MODULE_DIR_ . 'getresponse/views/templates/admin/',
            'action_url' => $this->context->link->getAdminLink('AdminGetresponseSubscribeRegistration'),
            'base_url',
            __PS_BASE_URI__
        ));

        $this->contactListService = ContactListServiceFactory::create();
    }

    public function initContent()
    {
        $this->display = 'view';
        $this->toolbar_title[] = $this->l('GetResponse');
        $this->toolbar_title[] = $this->l('Add Contacts During Registrations');
        parent::initContent();
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['add_campaign'] = array(
            'href' => self::$currentIndex . '&action=addCampaign&token=' . $this->getToken(),
            'desc' => $this->l('Add new contact list'),
            'icon' => 'process-icon-new'
        );

        parent::initPageHeaderToolbar();
    }

    /**
     * Get Admin Token
     * @return string
     */
    public function getToken()
    {
        return Tools::getAdminTokenLite('AdminGetresponseSubscribeRegistration');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('update' . $this->name)) {
            $this->display = 'edit';
        }

        if (Tools::isSubmit('addCampaignForm')) {
            $this->saveCampaign();
        }

        if (Tools::isSubmit('saveMappingForm')) {
            $this->saveCustom();
        }

        if (Tools::isSubmit('saveSubscribeForm')) {
            $this->performSubscribeViaRegistration();
        }

        parent::postProcess();
    }

    public function performSubscribeViaRegistration()
    {
        $addContactViaRegistrationDto = new SubscribeViaRegistrationDto(
            Tools::getValue('subscriptionSwitch'),
            Tools::getValue('newsletter', 0),
            Tools::getValue('campaign'),
            Tools::getValue('addToCycle', 0),
            Tools::getValue('cycledays'),
            Tools::getValue('contactInfo', 0)
        );

        $validator = new SubscribeViaRegistrationValidator($addContactViaRegistrationDto);
        if (!$validator->isValid()) {

            $this->errors = $validator->getErrors();

            return;
        }

        $this->contactListService->updateSubscribeViaRegistration($addContactViaRegistrationDto);
        $this->confirmations[] = $this->l('Settings saved');
    }

    /**
     * render main view
     * @return mixed
     * @throws GetresponseApiException
     */
    public function renderView()
    {
        $settings = $this->repository->getSettings();
        $isConnected = !empty($settings['api_key']) ? true : false;

        $this->context->smarty->assign(array(
            'is_connected' => $isConnected,
            'active_tracking' => $settings['active_tracking']
        ));

        if (Tools::getValue('action', null) === 'addCampaign') {

            $this->context->smarty->assign([
                'selected_tab' => 'subscribe_via_registration',
                'token' => $this->getToken(),
                'subscribe_via_registration_form' => $this->renderAddCampaignForm(
                    $this->getOptionForFromFields(),
                    $this->getOptionForReplayTo(),
                    $this->getOptionForSubject(),
                    $this->getOptionForBody()
                )
            ]);

        } else {

            $this->context->smarty->assign([
                'selected_tab' => 'subscribe_via_registration',
                'token' => $this->getToken(),
                'subscribe_via_registration_form' => $this->renderSubscribeRegistrationForm(
                    $this->getCampaignsOptions(),
                    $this->contactListService->getSettings()->getCycleDay()
                ),
                'subscribe_via_registration_list' =>$this->renderCustomList(),
                'campaign_days' => json_encode($this->getCampaignDays($this->contactListService->getAutoresponders())),
                'cycle_day' => $this->contactListService->getSettings()->getCycleDay(),
            ]);
        }

        return parent::renderView();
    }

    /**
     * @return array
     * @throws GetresponseApiException
     */
    private function getOptionForFromFields()
    {
        $options = [
            'id_option' => '',
            'name' => $this->l('Select from field')
        ];

        /** @var FromFields $fromField */
        foreach ($this->contactListService->getFromFields() as $fromField) {
            $options[] = [
                'id_option' => $fromField->getId(),
                'name' => $fromField->getName() . '(' . $fromField->getEmail() . ')'
            ];
        }

        return $options;
    }

    /**
     * @return array
     * @throws GetresponseApiException
     */
    private function getOptionForReplayTo()
    {
        $options = [
            'id_option' => '',
            'name' => $this->l('Select reply-to address')
        ];

        /** @var FromFields $fromField */
        foreach ($this->contactListService->getFromFields() as $fromField) {
            $options[] = [
                'id_option' => $fromField->getId(),
                'name' => $fromField->getName() . '(' . $fromField->getEmail() . ')'
            ];
        }

        return $options;
    }

    /**
     * @return array
     * @throws GetresponseApiException
     */
    private function getOptionForSubject()
    {
        $options = [
            'id_option' => '',
            'name' => $this->l('Select confirmation message subject')
        ];

        /** @var SubscriptionConfirmationSubject $subject */
        foreach ($this->contactListService->getSubscriptionConfirmationSubject() as $subject) {
            $options[] = [
                'id_option' => $subject->getId(),
                'name' => $subject->getSubject()
            ];
        }

        return $options;
    }

    /**
     * @param SubscriptionConfirmationSubjectCollection $subjectCollection
     * @return array
     */
    public function normalizeSubject(SubscriptionConfirmationSubjectCollection $subjectCollection)
    {
        $options = [];

        /** @var SubscriptionConfirmationSubject $subject */
        foreach ($subjectCollection as $subject) {
            $options[] = [
                'id_option' => $subject->getId(),
                'name' => $subject->getSubject()
            ];
        }

        return $options;
    }

    /**
     * @return array
     * @throws GetresponseApiException
     */
    public function getOptionForBody()
    {
        $options = [
            'id_option' => '',
            'name' => $this->l('Select confirmation message body template')
        ];

        /** @var SubscriptionConfirmationBody $confirmationBody */
        foreach ($this->contactListService->getSubscriptionConfirmationBody() as $confirmationBody) {
            $options[] = [
                'id_option' => $confirmationBody->getId(),
                'name' => $confirmationBody->getName() . ' ' . $confirmationBody->getContentPlain()
            ];
        }

        return $options;
    }

    /**
     * Returns subscribe on register form
     * @param array $campaigns
     * @param null|int $addToCycle
     * @return mixed
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
                            'value' => SubscribeViaRegistrationDto::UPDATE_CONTACT_ENABLED,
                            'label' => $this->l('Enabled')
                        ],
                        [
                            'id' => 'contact_off',
                            'value' => SubscribeViaRegistrationDto::UPDATE_CONTACT_DISABLED,
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
     * @return array
     * @throws GetresponseApiException
     */
    private function getCampaignsOptions()
    {
        $campaigns = [
            [
                'id_option' => 0,
                'name' => $this->l('Select a list')
            ]
        ];

        /** @var ContactList $contactList */
        foreach ($this->contactListService->getContactLists() as $contactList) {
            $campaigns[] = [
                'id_option' => $contactList->getId(),
                'name' => $contactList->getName()
            ];
        }

        return $campaigns;
    }

    /**
     * Renders custom list
     * @return mixed
     */
    public function renderList()
    {
        return $this->renderCustomList();
    }

    /**
     * Renders form for mapping edition
     *
     * @return string
     */
    public function renderForm()
    {
        $fieldsForm = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Update Mapping'),
                ],
                'input' => [
                    'id' => ['type' => 'hidden', 'name' => 'id'],
                    'customer_detail' => [
                        'label' => $this->l('Customer detail'),
                        'name' => 'customer_detail',
                        'type' => 'text',
                        'disabled' => true
                    ],
                    'gr_custom' => [
                        'label' => $this->l('Getresponse custom field name'),
                        'required' => true,
                        'desc' => $this->l('
                        You can use lowercase English alphabet characters, numbers, 
                        and underscore ("_"). Maximum 32 characters.
                    '),
                        'type' => 'text',
                        'name' => 'gr_custom'
                    ],
                    'default' => [
                        'required' => true,
                        'type' => 'hidden',
                        'name' => 'default'
                    ],
                    'mapping_on' => [
                        'type' => 'switch',
                        'label' => $this->l('Turn on this mapping'),
                        'name' => 'mapping_on',
                        'required' => true,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->l('Enabled')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->l('Disabled')]
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'saveMappingForm',
                    'icon' => 'process-icon-save'
                ]
            ]
        ];

        /** @var HelperFormCore $helper */
        $helper = new HelperForm();
        $helper->currentIndex = AdminController::$currentIndex;
        $helper->token = $this->getToken();
        $helper->fields_value = ['mapping_on' => false, 'gr_custom' => false, 'customer_detail' => false];

        $customs = $this->repository->getCustoms();
        foreach ($customs as $custom) {
            if (Tools::getValue('id') == $custom['id_custom']) {
                $helper->fields_value = [
                    'id' => $custom['id_custom'],
                    'customer_detail' => $custom['custom_field'],
                    'gr_custom' => $custom['custom_name'],
                    'default' => 0,
                    'mapping_on' => $custom['active_custom'] == 'yes' ? 1 : 0
                ];
            }
        }

        return $helper->generateForm(array(array('form' => $fieldsForm)));
    }

    /**
     * Assigns values to forms
     * @param $obj
     * @return array
     */
    public function getFieldsValue($obj)
    {
        if ($this->display == 'view') {
            $settings = $this->repository->getSettings();

            return [
                'subscriptionSwitch' => $settings['active_subscription'] == 'yes' ? 1 : 0,
                'campaign' => $settings['campaign_id'],
                'cycledays' => $settings['cycle_day'],
                'contactInfo' => $settings['update_address'] == 'yes' ? 1 : 0,
                'newsletter' => $settings['active_newsletter_subscription'] == 'yes' ? 1 : 0
            ];
        } else {
            $customs = $this->repository->getCustoms();
            foreach ($customs as $custom) {
                if (Tools::getValue('id') == $custom['id_custom']) {
                    return [
                        'id' => $custom['id_custom'],
                        'customer_detail' => $custom['custom_field'],
                        'gr_custom' => $custom['custom_name'],
                        'default' => 0,
                        'mapping_on' => $custom['active_custom'] == 'yes' ? 1 : 0,
                        'actions' => []
                    ];
                }
            }

            return [
                'id' => 1,
                'customer_detail' => '',
                'gr_custom' => '',
                'default' => 0,
                'on' => 0
            ];
        }
    }
}
