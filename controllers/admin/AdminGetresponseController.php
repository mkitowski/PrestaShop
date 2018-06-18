<?php
/**
 * @static $currentIndex
 * @property $display
 * @property $confirmations
 * @property $errors
 * @property $context
 * @property $toolbar_title
 * @property $module
 * @property $page_header_toolbar_btn
 * @property $bootstrap
 * @property $meta_title
 * @property $identifier
 * @property $show_form_cancel_button
 * @method string l() l($string, $class = null, $addslashes = false, $htmlentities = true)
 * @method void addJs() addJs($path)
 * @method void addJquery()
 * @method null initContent()
 */

use GetResponse\Account\AccountServiceFactory;
use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Api\ApiFactory;
use GrShareCode\ContactList\Autoresponder;
use GrShareCode\ContactList\AutorespondersCollection;
use GrShareCode\GetresponseApi;

class AdminGetresponseController extends ModuleAdminController
{
    /**
     * @var DbConnection
     * @deprecated
     * */
    public $db;

    /** @var GetResponseRepository */
    public $repository;

    public function __construct()
    {
        parent::__construct();

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }

        $this->db = new DbConnection(Db::getInstance(), GrShop::getUserShopId());

        $this->bootstrap = true;
        $this->meta_title = $this->l('GetResponse Integration');
        $this->identifier = 'api_key';

        $this->context->smarty->assign(array(
            'gr_tpl_path' => _PS_MODULE_DIR_ . 'getresponse/views/templates/admin/',
            'action_url' => $this->context->link->getAdminLink('AdminGetresponseAccount'),
            'base_url',
            __PS_BASE_URI__
        ));

        $this->repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());

        $account = AccountServiceFactory::create();

        if ('AdminGetresponseAccount' !== Tools::getValue('controller') && !$account->isConnectedToGetResponse()) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminGetresponseAccount'));
        }
    }

    /**
     * Set Css & js
     * @param bool $isNewTheme
     */
    public function setMedia($isNewTheme = false)
    {
        $this->context->controller->addJquery();
        $this->addJs(_MODULE_DIR_ . $this->module->name . '/views/js/gr-account.js');

        parent::setMedia($isNewTheme);
    }

    /**
     * Toolbar title
     */
    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('Administration');
        $this->toolbar_title[] = $this->l('Settings');
    }

    /**
     * Page Header Toolbar
     */
    public function initPageHeaderToolbar()
    {
        if (Tools::getValue('edit_id') != 'new') {
            parent::initPageHeaderToolbar();
        }

        unset($this->page_header_toolbar_btn['back']);
    }


    /**
     * Process Refresh Data
     * @return mixed
     */
    public function processRefreshData()
    {
        return $this->module->refreshDatas();
    }

    /**
     * Validate custom fields
     *
     * @param $customs
     *
     * @return array
     */
    public function validateCustoms($customs)
    {
        $errors = array();
        if (!is_array($customs)) {
            return array();
        }
        foreach ($customs as $custom) {
            if (!empty($custom) && preg_match('/^[\w\-]+$/', $custom) == false) {
                $errors[] = 'Error - "' . $custom . '" ' . $this->l('contains invalid characters');
            }
        }

        return $errors;
    }

    public function redirectIfNotAuthorized()
    {
        $settings = $this->repository->getSettings();

        if (empty($settings['api_key'])) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminGetresponse'));
        }
    }

    /**
     * @param AutorespondersCollection $autoresponders
     * @return array
     */
    public function getCampaignDays(AutorespondersCollection $autoresponders)
    {
        $campaignDays = [];

        /** @var Autoresponder $autoresponder */
        foreach ($autoresponders as $autoresponder) {
            $campaignDays[$autoresponder->getCampaignId()][$autoresponder->getCycleDay()] =
                [
                    'day' => $autoresponder->getCycleDay(),
                    'name' => $autoresponder->getSubject(),
                    'campaign_id' => $autoresponder->getCampaignId(),
                    'status' => $autoresponder->getStatus(),
                    'full_name' => '(' . $this->l('Day') . ': ' .
                        $autoresponder->getCycleDay() . ') ' . $autoresponder->getName() .
                        ' (' . $this->l('Subject') . ': ' . $autoresponder->getSubject() . ')'
                ];
        }

        return $campaignDays;
    }

    public function renderAddCampaignForm($fromFields, $replyTo, $confirmSubject, $confirmBody)
    {

        $fieldsForm = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Add new contact list'),
                    'icon' => 'icon-gears'
                ],
                'input' => [
                    'contact_list' => [
                        'label' => $this->l('List name'),
                        'name' => 'campaign_name',
                        'hint' => $this->l('You need to enter a name that\'s at least 3 characters long'),
                        'type' => 'text',
                        'required' => true
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('From field'),
                        'name' => 'from_field',
                        'required' => true,
                        'options' => [
                            'query' => $fromFields,
                            'id' => 'id_option',
                            'name' => 'name'
                        ]
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Reply-to'),
                        'name' => 'replyto',
                        'required' => true,
                        'options' => [
                            'query' => $replyTo,
                            'id' => 'id_option',
                            'name' => 'name'
                        ]
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Confirmation subject'),
                        'name' => 'subject',
                        'required' => true,
                        'options' => [
                            'query' => $confirmSubject,
                            'id' => 'id_option',
                            'name' => 'name'
                        ]
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Confirmation body'),
                        'name' => 'body',
                        'required' => true,
                        'desc' =>
                            $this->l(
                                'The confirmation message body and subject depend on System >> 
                            Configuration >> General >> Locale Options.'
                            ) .
                            '<br>' .
                            $this->l(
                                'By default all lists you create in Prestashop have double opt-in enabled.
                            You can change this later in your list settings.'
                            ),
                        'options' => [
                            'query' => $confirmBody,
                            'id' => 'id_option',
                            'name' => 'name'
                        ]
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'addCampaignForm',
                    'icon' => 'process-icon-save'
                ]
            ]
        ];

        /** @var HelperFormCore $helper */
        $helper = new HelperForm();
        $helper->currentIndex = AdminController::$currentIndex;
        $helper->token = $this->getToken();
        $helper->fields_value = [
            'campaign_name' => false,
            'from_field' => false,
            'replyto' => false,
            'subject' => false,
            'body' => false,
        ];

        return $helper->generateForm([['form' => $fieldsForm]]);
    }

    /**
     * Get Admin Token
     * @return bool|string
     */
    public function getToken()
    {
        return Tools::getAdminTokenLite('AdminGetresponse');
    }

    /**
     * Converts campaigns to display array
     *
     * @param $campaigns
     *
     * @return array
     */
    public function convertCampaignsToDisplayArray($campaigns)
    {
        $options = [
            [
                'id_option' => 0,
                'name' => $this->l('Select a list')
            ]
        ];

        foreach ($campaigns as $campaign) {
            $options[] = [
                'id_option' => $campaign['id'],
                'name' => $campaign['name']
            ];
        }

        return $options;
    }

    /**
     * Saves customs
     */
    public function saveCustom()
    {
        $custom = [
            'id' => Tools::getValue('id'),
            'value' => Tools::getValue('customer_detail'),
            'name' => Tools::getValue('gr_custom'),
            'active' => Tools::getValue('mapping_on') == 1 ? 'yes' : 'no'
        ];

        $error = $this->validateCustom($custom['name']);

        if (empty($error)) {
            $this->db->updateCustom($custom);
            $this->confirmations[] = $this->l('Custom sucessfuly edited');
        } else {
            $this->erors[] = $this->l($error);
        }
    }

    /**
     * @param string $custom
     * @return string
     */
    public function validateCustom($custom)
    {
        if (!empty($custom) && preg_match('/^[\w\-]+$/', $custom) == false) {
            return $this->l('Custom field contains invalid characters!');
        }
    }

    /**
     * @param string $name
     * @param array $list
     * @return array
     */
    public function prependOptionList($name, $list)
    {
        return array_merge([['id_option' => '', 'name' => $this->l($name)]], $list);
    }

    /**
     * Saves campaign
     */
    public function saveCampaign()
    {
        $name = Tools::getValue('campaign_name');
        $from = Tools::getValue('from_field');
        $to = Tools::getValue('replyto');
        $confirmSubject = Tools::getValue('subject');
        $confirmBody = Tools::getValue('body');

        if (strlen($name) < 4) {
            $this->errors[] = $this->l('The "list name" field is invalid');
        }
        if (strlen($from) < 4) {
            $this->errors[] = $this->l('The "from" field is required');
        }
        if (strlen($to) < 4) {
            $this->errors[] = $this->l('The "reply-to" field is required');
        }
        if (strlen($confirmSubject) < 4) {
            $this->errors[] = $this->l('The "confirmation subject" field is required');
        }
        if (strlen($confirmBody) < 4) {
            $this->errors[] = $this->l('The "confirmation body" field is required');
        }

        if (!empty($this->errors)) {
            $_GET['action'] = 'addCampaign';

            return;
        }

        try {
            $this->addCampaignToGR($name, $from, $to, $confirmSubject, $confirmBody);
            $this->confirmations[] = $this->l('List created');
        } catch (GrApiException $e) {
            $this->errors[] = $this->l('Contact list could not be added! (' . $e->getMessage() . ')');
        }
    }

    /**
     * @param string $campaignName
     * @param string $fromField
     * @param string $replyToField
     * @param string $confirmationSubject
     * @param string $confirmationBody
     * @throws GrApiException
     */
    public function addCampaignToGR(
        $campaignName,
        $fromField,
        $replyToField,
        $confirmationSubject,
        $confirmationBody
    ) {
        $settings = $this->repository->getSettings();
        // required params
        if (empty($settings['api_key'])) {
            return;
        }

        $api = $this->getGrAPI();

        try {
            $params = [
                'name' => $campaignName,
                'confirmation' => [
                    'fromField' => ['fromFieldId' => $fromField],
                    'replyTo' => ['fromFieldId' => $replyToField],
                    'subscriptionConfirmationBodyId' => $confirmationBody,
                    'subscriptionConfirmationSubjectId' => $confirmationSubject
                ],
                'languageCode' => 'EN'
            ];

            $campaign = $api->createCampaign($params);

            if (isset($campaign->codeDescription)) {
                throw new GrApiException($campaign->codeDescription, $campaign->code);
            }
        } catch (Exception $e) {
            throw GrApiException::createForCampaignNotAddedException($e);
        }
    }

    /**
     * @return GetresponseApi
     */
    public function getGrAPI()
    {
        $accountSettingsRepository = new AccountSettingsRepository(Db::getInstance(), GrShop::getUserShopId());
        $settings = $accountSettingsRepository->getSettings();

        return ApiFactory::createFromSettings($settings);
    }

    /**
     * Renders custom list
     * @return mixed
     */
    public function renderCustomList()
    {
        $fieldsList = [
            'customer_detail' => [
                'title' => $this->l('Customer detail'),
                'type' => 'text',
            ],
            'gr_custom' => [
                'title' => $this->l('Custom fields in GetResponse'),
                'type' => 'text',
            ],
            'on' => [
                'title' => $this->l('Active'),
                'type' => 'bool',
                'icon' => [
                    0 => 'disabled.gif',
                    1 => 'enabled.gif',
                    'default' => 'disabled.gif'
                ],
                'align' => 'center'
            ]
        ];

        /** @var HelperListCore $helper */
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->identifier = 'id';
        $helper->actions = array('edit');
        $helper->show_toolbar = true;

        $helper->title = $this->l('Contacts info');
        $helper->table = $this->name;
        $helper->token = $this->getToken();
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        return $helper->generateList($this->getCustomList(), $fieldsList);
    }

    /**
     * Returns custom list
     * @return array
     */
    public function getCustomList()
    {
        $customs = $this->repository->getCustoms();
        $result = array();
        foreach ($customs as $custom) {
            $result[] = [
                'id' => $custom['id_custom'],
                'customer_detail' => $custom['custom_field'],
                'gr_custom' => $custom['custom_name'],
                'default' => 0,
                'on' => $custom['active_custom'] == 'yes' ? 1 : 0
            ];
        }

        return $result;
    }
}
