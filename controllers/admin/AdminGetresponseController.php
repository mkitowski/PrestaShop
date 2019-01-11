<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author     Getresponse <grintegrations@getresponse.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use GetResponse\Account\AccountServiceFactory;
use GetResponse\Contact\Contact;
use GetResponse\ContactList\ContactListServiceFactory;
use GetResponse\CustomFields\GrCustomFieldsServiceFactory;
use GetResponse\CustomFieldsMapping\CustomFieldMapping;
use GetResponse\CustomFieldsMapping\CustomFieldMappingCollection;
use GetResponse\Helper\Shop as GrShop;
use GrShareCode\Api\Exception\GetresponseApiException;
use GrShareCode\ContactList\Autoresponder;
use GrShareCode\ContactList\AutorespondersCollection;
use GrShareCode\ContactList\ContactList;
use GrShareCode\CustomField\CustomField;
use GrShareCode\CustomField\CustomFieldCollection;

class AdminGetresponseController extends ModuleAdminController
{
    const DEFAULT_MAPPING = [
        [
            'plugin_field' => 'firstName',
            'getresponse_field' => 'firstname',
        ],
        [
            'plugin_field' => 'lastName',
            'getresponse_field' => 'lastname',
        ],
        [
            'plugin_field' => 'email',
            'getresponse_field' => 'email',
        ]
    ];

    const PRESTASHOP_FIELDS = [
        'address',
        'postalCode',
        'city',
        'phone',
        'country',
        'birthDate',
        'company',
    ];

    /** @var string */
    protected $name;

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

        try {
            $accountService = AccountServiceFactory::create();
            $isConnectedToGetResponse = $accountService->isConnectedToGetResponse();
        } catch (GetresponseApiException $e) {
            $isConnectedToGetResponse = false;
        }

        if ('AdminGetresponseAccount' !== Tools::getValue('controller') && !$isConnectedToGetResponse) {
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
     * @param CustomFieldMappingCollection|null $selectedCustomFields
     * @return string
     * @throws SmartyException
     */
    public function renderCustomList(CustomFieldMappingCollection $selectedCustomFields)
    {
        try {
            $customFieldsService = GrCustomFieldsServiceFactory::create();
            $grCustomsCollection = $customFieldsService->getAllCustomFields();
        } catch (GetresponseApiException $e) {
            $grCustomsCollection = new CustomFieldCollection();
        }

        $getresponseFields = [];
        /** @var CustomField $custom */
        foreach ($grCustomsCollection as $custom) {
            if ($custom->getName() != Contact::ORIGIN) {
                $getresponseFields[] = [
                    'id' => $custom->getId(),
                    'name' => $custom->getName(),
                ];
            }
        }

        $this->context->smarty->assign([
            'customs' => [
                'plugin_field' => self::PRESTASHOP_FIELDS,
                'getresponse_field' => $getresponseFields,
                'defaults' => self::DEFAULT_MAPPING,
                'selected' => $selectedCustomFields->toArray()
            ]
        ]);

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'getresponse/views/templates/admin/common/custom_fields.tpl'
        );
    }

    /**
     * @return CustomFieldMappingCollection
     */
    protected function getCustomFieldsFromPost()
    {
        $customFieldMappingCollection = new CustomFieldMappingCollection();

        if (0 == Tools::getValue('contactInfo', 0)) {
            return $customFieldMappingCollection;
        }

        $pluginFields = Tools::getValue('plugin_fields', []);
        $getresponseFields = Tools::getValue('gr_fields', []);

        foreach ($pluginFields as $index => $value) {
            $customFieldMappingCollection->add(
                new CustomFieldMapping($value, $getresponseFields[$index])
            );
        }

        return $customFieldMappingCollection;
    }

    /**
     * @return array
     * @throws GetresponseApiException
     */
    protected function getCampaignsOptions()
    {
        $contactListService = ContactListServiceFactory::create();

        $campaigns = [
            [
                'id_option' => 0,
                'name' => $this->l('Select a list')
            ]
        ];

        /** @var ContactList $contactList */
        foreach ($contactListService->getContactLists() as $contactList) {
            $campaigns[] = [
                'id_option' => $contactList->getId(),
                'name' => $contactList->getName()
            ];
        }

        return $campaigns;
    }
}
