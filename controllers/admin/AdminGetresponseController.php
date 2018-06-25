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
use GetResponse\Helper\Shop as GrShop;

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
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name . '&referer=' . $this->controller_name;

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
