<?php
require_once 'AdminGetresponseController.php';

use GetResponse\CustomFieldsMapping\CustomFieldMapping;
use GetResponse\CustomFieldsMapping\CustomFieldMappingException;
use GetResponse\CustomFieldsMapping\CustomFieldMappingServiceFactory;
use GetResponse\CustomFieldsMapping\CustomFieldMappingValidator;
use GetResponse\Helper\FlashMessages;
use GrShareCode\GetresponseApiException;

class AdminGetresponseUpdateMappingController extends AdminGetresponseController
{
    public $name = 'GRUpdateMapping';

    /** @var CustomFieldsMappingService */
    private $mappingService;

    public function __construct()
    {
        parent::__construct();
        $this->addJquery();
        $this->addJs(_MODULE_DIR_ . $this->module->name . '/views/js/gr-registration.js');

        $this->mappingService = CustomFieldMappingServiceFactory::create();
    }

    public function initContent()
    {
        $this->display = 'edit';
        $this->toolbar_title[] = $this->l('GetResponse');
        $this->toolbar_title[] = $this->l('Update mapping');
        parent::initContent();
    }

    /**
     * @throws GetresponseApiException
     * @throws CustomFieldMappingException
     */
    public function postProcess()
    {
        if (Tools::isSubmit('saveMappingForm')) {

            $custom = [
                'id' => Tools::getValue('id'),
                'value' => Tools::getValue('customer_detail'),
                'name' => Tools::getValue('gr_custom'),
                'active' => Tools::getValue('mapping_on'),
                'default' => Tools::getValue('default')
            ];

            $validator = new CustomFieldMappingValidator($custom);

            if (!$validator->isValid()) {
                $this->errors = $validator->getErrors();

                return;
            }

            $this->mappingService->updateCustomFieldMapping(CustomFieldMapping::createFromRequest($custom));
            FlashMessages::add(FlashMessages::TYPE_CONFIRMATION, $this->l('Custom sucessfuly edited'));
            Tools::redirectAdmin($this->context->link->getAdminLink(Tools::getValue('referer')));
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
        $helper->token = Tools::getAdminTokenLite('AdminGetresponseUpdateMapping');
        $helper->fields_value = ['mapping_on' => false, 'gr_custom' => false, 'customer_detail' => false];

        $customFieldMapping = $this->mappingService->getCustomFieldMappingById(Tools::getValue('id'));

        if ($customFieldMapping) {
            $helper->fields_value = [
                'id' => $customFieldMapping->getId(),
                'customer_detail' => $customFieldMapping->getField(),
                'gr_custom' => $customFieldMapping->getName(),
                'default' => $customFieldMapping->isDefault() ? 1 : 0,
                'mapping_on' => $customFieldMapping->isActive() ? 1 : 0
            ];
        }

        return $helper->generateForm([$fieldsForm]);
    }

}
