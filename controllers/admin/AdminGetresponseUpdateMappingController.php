<?php
require_once 'AdminGetresponseController.php';

use GetResponse\ContactList\ContactListService;
use GetResponse\ContactList\ContactListServiceFactory;
use GrShareCode\GetresponseApiException;

class AdminGetresponseUpdateMappingController extends AdminGetresponseController
{
    public $name = 'GRUpdateMapping';

    /** @var ContactListService */
    private $contactListService;

    public function __construct()
    {
        parent::__construct();
        $this->addJquery();
        $this->addJs(_MODULE_DIR_ . $this->module->name . '/views/js/gr-registration.js');

        $this->context->smarty->assign([
            'gr_tpl_path' => _PS_MODULE_DIR_ . 'getresponse/views/templates/admin/',
            'action_url' => $this->context->link->getAdminLink('AdminGetresponseSubscribeRegistration'),
            'base_url',
            __PS_BASE_URI__
        ]);

        $this->contactListService = ContactListServiceFactory::create();
    }

    public function initContent()
    {
        $this->display = 'view';
        $this->toolbar_title[] = $this->l('GetResponse');
        $this->toolbar_title[] = $this->l('Update mapping');
        parent::initContent();
    }

    /**
     * @throws GetresponseApiException
     */
    public function postProcess()
    {
        if (Tools::isSubmit('saveMappingForm')) {
            $custom = [
                'id' => Tools::getValue('id'),
                'value' => Tools::getValue('customer_detail'),
                'name' => Tools::getValue('gr_custom'),
                'active' => Tools::getValue('mapping_on') == 1 ? 'yes' : 'no'
            ];

            if (!empty($custom) && preg_match('/^[\w\-]+$/', $custom) == false) {
                $error = $this->l('Custom field contains invalid characters!');
            }

            if (empty($error)) {
                $this->db->updateCustom($custom);
                $this->confirmations[] = $this->l('Custom sucessfuly edited');
            } else {
                $this->erors[] = $this->l($error);
            }
        }
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
        $helper->currentIndex = AdminController::$currentIndex . '&referer=' . Tools::getValue('referer');
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

        return $helper->generateForm([$fieldsForm]);
    }


    /**
     * Get Admin Token
     * @return string
     */
    public function getToken()
    {
        return Tools::getAdminTokenLite('AdminGetresponseUpdateMapping');
    }

}
