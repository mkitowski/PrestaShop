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

use GetResponse\Account\AccountSettingsRepository;
use GetResponse\Account\AccountStatusFactory;
use GetResponse\Api\ApiFactory;
use GetResponse\Helper\Shop as GrShop;
use GrShareCode\ContactList\Autoresponder;
use GrShareCode\ContactList\AutorespondersCollection;
use GrShareCode\GetresponseApi;

class AdminGetresponseController extends ModuleAdminController
{
    /** @var GetResponseRepository */
    public $repository;

    public function __construct()
    {
        parent::__construct();

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }

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

        $accountStatus = AccountStatusFactory::create();

        if ('AdminGetresponseAccount' !== Tools::getValue('controller') && !$accountStatus->isConnectedToGetResponse()) {
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
     * Renders custom list
     * @return string
     * @throws PrestaShopDatabaseException
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
        $helper->token = Tools::getAdminTokenLite('AdminGetresponseUpdateMapping');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminGetresponseUpdateMapping', false). '&configure=' . $this->name . '&referer=' . $this->controller_name;

        return $helper->generateList($this->getCustomList(), $fieldsList);
    }
 
    /**
     * Returns custom list
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getCustomList()
    {
        $customs = $this->repository->getCustoms();
        $result = [];
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
