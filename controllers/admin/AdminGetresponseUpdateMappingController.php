<?php
require_once 'AdminGetresponseController.php';

use GetResponse\CustomFields\CustomFieldsServiceFactory;
use GetResponse\CustomFieldsMapping\CustomFieldMapping;
use GetResponse\CustomFieldsMapping\CustomFieldMappingException;
use GetResponse\CustomFieldsMapping\CustomFieldMappingServiceFactory;
use GetResponse\CustomFieldsMapping\CustomFieldMappingValidator;
use GetResponse\CustomFieldsMapping\CustomFieldsMappingService;
use GetResponse\Helper\FlashMessages;
use GrShareCode\Api\Exception\GetresponseApiException;
use GrShareCode\CustomField\CustomField;

class AdminGetresponseUpdateMappingController extends AdminGetresponseController
{
    /** @var CustomFieldsMappingService */
    private $mappingService;

    public function __construct()
    {
        parent::__construct();
        $this->addJquery();
        $this->addJs(_MODULE_DIR_ . $this->module->name . '/views/js/gr-registration.js');
        $this->name = 'GRUpdateMapping';

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

            $customFieldMapping = $this->mappingService->getCustomFieldMappingById(Tools::getValue('id'));

            $custom = [
                'id' => (int) Tools::getValue('id'),
                'custom_name' => $customFieldMapping->getCustomName(),
                'customer_property_name' => $customFieldMapping->getCustomerPropertyName(),
                'gr_custom_id' => Tools::getValue('gr_custom'),
                'is_active' => (bool) Tools::getValue('mapping_on'),
                'is_default' => (bool) Tools::getValue('default')
            ];

            $validator = new CustomFieldMappingValidator($custom);

            if (!$validator->isValid()) {
                $this->errors = $validator->getErrors();
                return;
            }

            $this->mappingService->updateCustomFieldMapping(CustomFieldMapping::createFromArray($custom));
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
                        'type' => 'select',
                        'label' => $this->l('Getresponse custom field name'),
                        'required' => true,
                        'class' => 'gr-select',
                        'desc' => $this->l('You can use lowercase English alphabet characters, numbers, and underscore ("_"). Maximum 32 characters.'),
                        'name' => 'gr_custom',
                        'options' => [
                            'query' => $this->getCustomFieldsToSelect(),
                            'id' => 'grCustomFieldId',
                            'name' => 'name'
                        ]
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
                'customer_detail' => $customFieldMapping->getCustomerPropertyName(),
                'gr_custom' => $customFieldMapping->getGrCustomId(),
                'default' => (int) $customFieldMapping->isDefault(),
                'mapping_on' => (int) $customFieldMapping->isActive()
            ];
        }

        return $helper->generateForm([$fieldsForm]);
    }

    /**
     * @return array
     * @throws GetresponseApiException
     * @throws \GrShareCode\Api\Authorization\ApiTypeException
     */
    private function getCustomFieldsToSelect()
    {
        $customFieldsForSelect = [];
        $customFieldService = CustomFieldsServiceFactory::create();

        $grCustomFields = $customFieldService->getCustomFieldsFromGetResponse();

        /** @var CustomField $customField */
        foreach ($grCustomFields as $customField) {
            $customFieldsForSelect[] = [
                'grCustomFieldId' => $customField->getId(),
                'name' => $customField->getName()
            ];
        }

        return $customFieldsForSelect;

    }

}
