<?php
require_once 'AdminGetresponseController.php';

use GrShareCode\Campaign\CampaignService as GrCampaignService;

class AdminGetresponseSubscribeRegistrationController extends AdminGetresponseController
{
    public $name = 'GRSubscribeRegistration';

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


    /**
     * Get Admin Token
     * @return string
     */
    public function getToken()
    {
        return Tools::getAdminTokenLite('AdminGetresponseSubscribeRegistration');
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
            'is_connected' => $isConnected,
            'active_tracking' => $settings['active_tracking']
        ));

        if (Tools::getValue('action', null) == 'addCampaign') {
//            $api = $this->getGrAPI();

            $settings = $this->repository->getSettings();
            $api = new GrApi($settings['api_key'], $settings['account_type'], $settings['crypto']);

            $fromFields = $this->normalizeFormFields($api->getFromFields());
            $confirmSubject = $this->normalizeComplexApiData(
                $api->getSubscriptionConfirmationsSubject(),
                'id',
                'name'
            );

            $confirmBody = $this->normalizeComplexApiData(
                $api->getSubscriptionConfirmationsBody(),
                'id',
                'name',
                'contentPlain'
            );

            $this->context->smarty->assign(array(
                'selected_tab' => 'subscribe_via_registration',
                'subscribe_via_registration_form' => $this->renderAddCampaignForm(
                    $this->prependOptionList('Select from field', $fromFields),
                    $this->prependOptionList('Select reply-to address', $fromFields),
                    $this->prependOptionList('Select confirmation message subject', $confirmSubject),
                    $this->prependOptionList('Select confirmation message body template', $confirmBody)
                ),
                'token' => $this->getToken(),
            ));
        } else {
            $this->subscribeViaRegistrationView();
        }

        return parent::renderView();
    }

    public function performSubscribeViaRegistration()
    {
        $this->redirectIfNotAuthorized();
        $subscription = Tools::getValue('subscriptionSwitch') == 1 ? 'yes' : 'no';
        $campaign = Tools::getValue('campaign');
        $addToCycle = Tools::getValue('addToCycle', 0);
        $cycleDay = Tools::getValue('cycledays');
        $updateAddress = Tools::getValue('contactInfo', 0) == 1 ? 'yes' : 'no';
        $newsletter = Tools::getValue('newsletter', 0) == 1 ? 'yes' : 'no';

        if ((empty($campaign) || $campaign == '0') && $subscription === 'yes') {
            $this->errors[] = $this->l('You need to select list');

            return;
        }

        $cycleDay = 1 == $addToCycle ? $cycleDay : null;
        $this->repository->updateSettings($subscription, $campaign, $updateAddress, $cycleDay, $newsletter);

        $this->confirmations[] = $this->l('Settings saved');
    }

    public function normalizeFormFields($data, $options = array())
    {
        foreach ($data as $row) {
            $options[] = array(
                'id_option' => $row['id'],
                'name' => $row['name'] . '(' . $row['email'] . ')'
            );
        }

        return $options;
    }

    /**
     * @param array $data
     * @param string $identifier
     * @param string $name
     * @param null|string $complex
     * @param array $options
     * @return array
     */
    public function normalizeComplexApiData($data, $identifier, $name, $complex = null, $options = array())
    {
        foreach ($data as $row) {
            $options[] = array(
                'id_option' => $row[$identifier],
                'name' => $row[$name] . ' ' . ($complex != null ? $row[$complex] : '')
            );
        }

        return $options;
    }

    /**
     * Subscription via registration page
     */
    public function subscribeViaRegistrationView()
    {
        $settings = $this->repository->getSettings();
        $api = $this->getGrAPI();
        $campaignService = new GrCampaignService($api);

        $this->context->smarty->assign(array(
            'selected_tab' => 'subscribe_via_registration',
            'subscribe_via_registration_form' => $this->renderSubscribeRegistrationForm(
                $this->getCampaigns($campaignService),
                $settings['cycle_day']
            ),
            'subscribe_via_registration_list' => $this->renderList(),
            'campaign_days' => json_encode($this->getCampaignDays($campaignService->getAutoResponders())),
            'cycle_day' => $settings['cycle_day'],
            'token' => $this->getToken(),
        ));
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

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Subscribe after Customer Register'),
                'icon' => 'icon-gears'
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Add contacts to GetResponse during registration'),
                    'name' => 'subscriptionSwitch',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Include Prestashop newsletter subscribers'),
                    'name' => 'newsletter',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'newsletter_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'newsletter_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Contact list'),
                    'name' => 'campaign',
                    'required' => true,
                    'desc' =>
                        '<input type="checkbox" id="addToCycle" value="1" name="addToCycle" ' .
                        $addToCycle . '> ' . $this->l('Add to autoresponder cycle'),
                    'options' => array(
                        'query' => $campaigns,
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Autoresponder Day'),
                    'name' => 'cycledays',
                    'options' => array(
                        'query' => array(),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
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
                    'values' => array(
                        array('id' => 'contact_on', 'value' => 1, 'label' => $this->l('Enabled')),
                        array('id' => 'contact_off', 'value' => 0, 'label' => $this->l('Disabled'))
                    )
                )
            ),
            'submit' =>
                array(
                    'title' => $this->l('Save'),
                    'name' => 'saveSubscribeForm',
                    'icon' => 'process-icon-save'
                )
        );

        return parent::renderForm();
    }

    /**
     * @param GrCampaignService $campaignService
     */
    private function getCampaigns($campaignService)
    {
        $campaignsCollection = $campaignService->getAllCampaigns();
        $campaigns = array(array('id_option' => 0, 'name' => $this->l('Select a list')));

        /** @var \GrShareCode\Campaign\Campaign $campaignItem */
        foreach ($campaignsCollection as $campaignItem) {
            $campaigns[] = array('id_option' => $campaignItem->getId(), 'name' => $campaignItem->getName());
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
     * @return mixed
     */
    public function renderForm()
    {
        $fieldsForm = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Update Mapping'),
                ),
                'input' => array(
                    'id' => array('type' => 'hidden', 'name' => 'id'),
                    'customer_detail' => array(
                        'label' => $this->l('Customer detail'),
                        'name' => 'customer_detail',
                        'type' => 'text',
                        'disabled' => true
                    ),
                    'gr_custom' => array(
                        'label' => $this->l('Getresponse custom field name'),
                        'required' => true,
                        'desc' => $this->l('
                        You can use lowercase English alphabet characters, numbers, 
                        and underscore ("_"). Maximum 32 characters.
                    '),
                        'type' => 'text',
                        'name' => 'gr_custom'
                    ),
                    'default' => array(
                        'required' => true,
                        'type' => 'hidden',
                        'name' => 'default'
                    ),
                    'mapping_on' => array(
                        'type' => 'switch',
                        'label' => $this->l('Turn on this mapping'),
                        'name' => 'mapping_on',
                        'required' => true,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Enabled')),
                            array('id' => 'active_off', 'value' => 0, 'label' => $this->l('Disabled'))
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'saveMappingForm',
                    'icon' => 'process-icon-save'
                )
            )
        );

        /** @var HelperFormCore $helper */
        $helper = new HelperForm();
        $helper->currentIndex = AdminController::$currentIndex;
        $helper->token = $this->getToken();
        $helper->fields_value = ['mapping_on' => false, 'gr_custom' => false, 'customer_detail' => false];

        $customs = $this->repository->getCustoms();
        foreach ($customs as $custom) {
            if (Tools::getValue('id') == $custom['id_custom']) {
                $helper->fields_value = array(
                    'id' => $custom['id_custom'],
                    'customer_detail' => $custom['custom_field'],
                    'gr_custom' => $custom['custom_name'],
                    'default' => 0,
                    'mapping_on' => $custom['active_custom'] == 'yes' ? 1 : 0
                );
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

            return array(
                'subscriptionSwitch' => $settings['active_subscription'] == 'yes' ? 1 : 0,
                'campaign' => $settings['campaign_id'],
                'cycledays' => $settings['cycle_day'],
                'contactInfo' => $settings['update_address'] == 'yes' ? 1 : 0,
                'newsletter' => $settings['active_newsletter_subscription'] == 'yes' ? 1 : 0
            );
        } else {
            $customs = $this->repository->getCustoms();
            foreach ($customs as $custom) {
                if (Tools::getValue('id') == $custom['id_custom']) {
                    return array(
                        'id' => $custom['id_custom'],
                        'customer_detail' => $custom['custom_field'],
                        'gr_custom' => $custom['custom_name'],
                        'default' => 0,
                        'mapping_on' => $custom['active_custom'] == 'yes' ? 1 : 0,
                        'actions' => array()
                    );
                }
            }

            return array(
                'id' => 1,
                'customer_detail' => '',
                'gr_custom' => '',
                'default' => 0,
                'on' => 0
            );
        }
    }
}
