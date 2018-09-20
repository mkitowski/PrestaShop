<?php

require_once 'AdminGetresponseController.php';

use GetResponse\ContactList\ContactListService;
use GetResponse\ContactList\ContactListServiceFactory;
use GetResponse\CustomFieldsMapping\CustomFieldMappingServiceFactory;
use GetResponse\Export\ExportSettings;
use GrShareCode\Api\ApiTypeException;
use GrShareCode\Contact\ContactService as GrContactService;
use GrShareCode\ContactList\ContactList;
use GrShareCode\ContactList\FromFields;
use GrShareCode\ContactList\FromFieldsCollection;
use GrShareCode\GetresponseApiException;

class AdminGetresponseExportController extends AdminGetresponseController
{
    public $name = 'AdminGetresponseExport';

    /** @var ContactListService */
    public $contactListService;

    public function __construct()
    {
        parent::__construct();
        $this->addJquery();
        $this->addJs(_MODULE_DIR_ . $this->module->name . '/views/js/gr-export.js');
        $this->contactListService = ContactListServiceFactory::create();
    }

    /**
     * @return bool|ObjectModel|void
     * @throws GetresponseApiException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function postProcess()
    {
        if (Tools::isSubmit($this->name)) {

            $exportDto = new ExportSettings(
                Tools::getValue('campaign'),
                Tools::getValue('addToCycle_1', 0) == 1 ? Tools::getValue('autoresponder_day', null) : null,
                Tools::getValue('contactInfo', 0) ==  1 ? true : false,
                Tools::getValue('newsletter', 0) ==  1 ? true : false,
                Tools::getValue('exportEcommerce_1', 0) ==  1 ? true : false,

            );

            if (empty($exportDto->getContactListId())) {

                $this->contactListService->updateSubscribeViaRegistration($addContactViaRegistrationDto);
                FlashMessages::add(FlashMessages::TYPE_CONFIRMATION, $this->l('Settings saved'));
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminGetresponseSubscribeRegistration'));

                $this->errors[] = $this->l('You need to select list');
                $this->exportCustomersView();
                return;
            }

            try {
                $export = new GrExport($exportSettings, $this->repository);
                $export->export();
            } catch (GetresponseApiException $e) {
                $this->errors[] = $this->l($e->getMessage());
                $this->exportCustomersView();
                return;
            } catch (PrestaShopDatabaseException $e) {
                $this->errors[] = $this->l($e->getMessage());
                $this->exportCustomersView();
                return;
            } catch (PrestaShopException $e) {
                $this->errors[] = $this->l($e->getMessage());
                $this->exportCustomersView();
                return;
            }

            $this->confirmations[] = $this->l('Customer data exported');

            $this->exportCustomersView();
        }
        parent::postProcess();
    }

    public function initContent()
    {
        $this->display = 'view';

        if (Tools::isSubmit('update' . $this->name)) {
            $this->display = 'edit';
        }

        parent::initContent();
    }

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
     * Render main view
     * @return string
     * @throws GetresponseApiException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function renderView()
    {
        $this->exportCustomersView();

        return parent::renderView();
    }

    /**
     * @throws GetresponseApiException
     * @throws ApiTypeException
     */
    public function exportCustomersView()
    {
        $settings = $this->repository->getSettings();

        $this->context->smarty->assign([
            'selected_tab' => 'export_customers',
            'export_customers_form' => $this->renderExportForm(),
            'export_customers_list' => $this->renderCustomList(),
            'campaign_days' => json_encode($this->getCampaignDays($this->contactListService->getAutoresponders())),
            'cycle_day' => $settings['cycle_day'],
            'token' => $this->getToken(),
        ]);
    }

    /**
     * Get Admin Token
     * @return string
     */
    public function getToken()
    {
        return Tools::getAdminTokenLite('AdminGetresponseExport');
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('GetResponse');
        $this->toolbar_title[] = $this->l('Export Customer Data on Demand');
    }

    /**
     * @return array
     * @throws GetresponseApiException
     */
    private function getCampaigns()
    {
        $campaigns = [];

        /** @var ContactList $contactList */
        foreach ($this->contactListService->getContactLists() as $contactList) {
            $campaigns[] = [
                'id' => $contactList->getId(),
                'name' => $contactList->getName()
            ];
        }

        return $campaigns;
    }

    /**
     * @return string
     * @throws GetresponseApiException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function renderExportForm()
    {
        $fieldsForm = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Export Your Customer Information From PrestaShop to your GetResponse Account')
                ],
                'description' => $this->l('Use this option for one time export of your existing customers.'),
                'input' => [
                    ['type' => 'hidden', 'name' => 'autoresponders'],
                    ['type' => 'hidden', 'name' => 'cycle_day_selected'],
                    [
                        'type' => 'select',
                        'name' => 'campaign',
                        'required' => true,
                        'label' => $this->l('Contact list'),
                        'options' => [
                            'query' => [
                                ['id' => '', 'name' => $this->l('Select a list')]
                                ] + $this->getCampaigns(),
                            'id' => 'id',
                            'name' => 'name'
                        ]
                    ],
                    [
                        'label' => $this->l('Include newsletter subscribers'),
                        'name' => 'newsletter',
                        'type' => 'switch',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'newsletter_on', 'value' => 1, 'label' => $this->l('Yes')],
                            ['id' => 'newsletter_off', 'value' => 0, 'label' => $this->l('No')]
                        ]
                    ],
                    [
                        'type' => 'checkbox',
                        'label' => '',
                        'name' => 'addToCycle',
                        'values' => [
                            'query' => [
                                ['id' => 1, 'val' =>1, 'name' => $this->l(' Add to autoresponder cycle')]
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Autoresponder day'),
                        'class'    => 'gr-select',
                        'name' => 'autoresponder_day',
                        'data-default' => $this->l('no autoresponders'),
                        'options' => [
                            'query' => [['id' => '', 'name' => $this->l('no autoresponders')]],
                            'id' => 'id',
                            'name' => 'name'
                        ]
                    ],
                    [
                        'type' => 'checkbox',
                        'label' => '',
                        'name' => 'exportEcommerce',
                        'values' => [
                            'query' => [
                                ['id' => 1, 'val' =>1, 'name' => $this->l(' Include ecommerce data in this export')]
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'label' => $this->l('Update contacts info'),
                        'name' => 'contactInfo',
                        'type' => 'switch',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'update_on', 'value' => 1, 'label' => $this->l('Yes')],
                            ['id' => 'update_off', 'value' => 0, 'label' => $this->l('No')]
                        ],
                        'desc' =>
                            $this->l('
                                Select this option if you want to overwrite contact details that 
                                already exist in your GetResponse database.
                            ') .
                            '<br>' .
                            $this->l('Clear this option to keep existing data intact.')
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Export'),
                    'icon' => 'process-icon-download',
                    'name' => $this->name
                ]
            ]
        ];

        /** @var HelperFormCore $helper */
        $helper = new HelperFormCore();
        $helper->currentIndex = AdminController::$currentIndex;
        $helper->token = $this->getToken();
        $helper->fields_value = [
            'campaign' => false,
            'autoresponder_day' => false,
            'contactInfo' => Tools::getValue('mapping', 0),
            'newsletter' => 0,
            'autoresponders' => json_encode([]),
            'cycle_day_selected' => 0
        ];

        return $helper->generateForm([$fieldsForm]) . $this->renderList();
    }

    /**
     * Assigns values to forms
     * @param ObjectModel $obj
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getFieldsValue($obj)
    {
        if (Tools::getValue('action', null) == 'addCampaign') {
            return ['campaign_name' => null];
        }

        if ($this->display == 'view') {

            return array(
                'campaign' => Tools::getValue('campaign', null),
                'autoresponder_day' => Tools::getValue('autoresponder_day', null),
                'contactInfo' => Tools::getValue('contactInfo', null),
                'newsletter' => Tools::getValue('newsletter', null)
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
                        'mapping_on' => $custom['active_custom'] == 'yes' ? 1 : 0
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
