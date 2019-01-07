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
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

require_once 'AdminGetresponseController.php';

use GetResponse\Account\AccountServiceFactory;
use GetResponse\ContactList\ContactListServiceFactory;
use GetResponse\Helper\FlashMessages;
use GetResponse\Settings\Registration\RegistrationServiceFactory;
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
        $this->addJs(_MODULE_DIR_ . $this->module->name . '/views/js/gr-custom-fields.js');

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
        $link = (new LinkCore())->getAdminLink('AdminGetresponseAddNewContactList');
        $link .= '&referer=' . $this->controller_name;

        $this->page_header_toolbar_btn['add_campaign'] = [
            'href' => $link,
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
        if (!Tools::isSubmit('saveSubscribeForm')) {
            return;
        }

        $service = RegistrationServiceFactory::createService();

        if (0 == Tools::getValue('subscriptionSwitch', 0)) {
            $service->clearSettings();
            return;
        }

        $cycleDay = null;
        $addToCycle = Tools::getValue('addToCycle', 0);
        if ($addToCycle) {
            $cycleDay = Tools::getValue('cycledays', null);
        }

        $registrationSettings = new RegistrationSettings(
            true,
            1 == Tools::getValue('newsletter', 0),
            Tools::getValue('campaign', null),
            $cycleDay,
            $this->getCustomFieldsFromPost()
        );

        $validator = new RegistrationSettingsValidator($registrationSettings);

        if (!$validator->isValid()) {
            $this->errors = $validator->getErrors();
            return;
        }

        $service->updateSettings($registrationSettings);

        FlashMessages::add(FlashMessages::TYPE_CONFIRMATION, $this->l('Settings saved'));
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminGetresponseSubscribeRegistration'));
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
        $registrationService = RegistrationServiceFactory::createService();
        $registrationSettings = $registrationService->getSettings();
        $contactListService = $contactListService = ContactListServiceFactory::create();

        $this->context->smarty->assign([
            'is_connected' => $accountSettings->isConnectedWithGetResponse(),
            'selected_tab' => 'subscribe_via_registration',
            'token' => $this->getToken(),
            'subscribe_via_registration_form' => $this->renderSubscribeRegistrationForm(
                $this->getCampaignsOptions(),
                $registrationSettings
            ),
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
     * @param RegistrationSettings $registrationSettings
     * @return mixed
     * @throws SmartyException
     */
    public function renderSubscribeRegistrationForm($campaigns = array(), $registrationSettings)
    {
        $addToCycle = $registrationSettings->getCycleDay();

        if (is_string($addToCycle) && Tools::strlen($addToCycle) > 0) {
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
                    ],
                ],
                [
                    'type' => 'html',
                    'name' => 'customs',
                    'html_content' => $this->renderCustomList($registrationSettings->getCustomFieldMappingCollection()),
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
     * Assigns values to forms
     * @param ObjectModel $obj
     * @return array
     */
    public function getFieldsValue($obj)
    {
        $service = RegistrationServiceFactory::createService();
        $settings = $service->getSettings();

        return [
            'subscriptionSwitch' => $settings->isActive() ? 1 : 0,
            'campaign' => $settings->getListId(),
            'cycledays' => $settings->getCycleDay(),
            'contactInfo' => $settings->getCustomFieldMappingCollection()->count() > 0,
            'newsletter' => $settings->isNewsletterActive() ? 1 : 0
        ];
    }
}
