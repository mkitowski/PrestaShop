<?php

use GetResponse\WebForm\WebForm;
use GetResponse\WebForm\WebFormDto;
use GetResponse\WebForm\WebFormServiceFactory;
use GetResponse\WebForm\WebFormValidator;
use GrShareCode\WebForm\WebForm as GetResponseForm;
use GrShareCode\WebForm\WebFormCollection;

require_once 'AdminGetresponseController.php';

class AdminGetresponseSubscribeFormController extends AdminGetresponseController
{
    /** @var \GetResponse\WebForm\WebFormService */
    private $webFormService;

    /** @var WebFormCollection */
    private $getResponseWebFormCollection;

    public function __construct()
    {
        parent::__construct();
        $this->addJquery();
        $this->addJs(_MODULE_DIR_ . $this->module->name . '/views/js/gr-webform.js');

        $this->context->smarty->assign(array(
            'gr_tpl_path' => _PS_MODULE_DIR_ . 'getresponse/views/templates/admin/',
            'action_url' => $this->context->link->getAdminLink('AdminGetresponseSubscribeForm'),
            'selected_tab' => 'subscribe_via_registration'
        ));

        $this->webFormService = WebFormServiceFactory::create();
        $this->getResponseWebFormCollection = $this->webFormService->getGetResponseFormCollection();
    }

    public function initContent()
    {
        $this->display = 'edit';
        $this->show_form_cancel_button = false;
        $this->toolbar_title[] = $this->l('GetResponse');
        $this->toolbar_title[] = $this->l('Add Contacts via GetResponse Forms');

        parent::initContent();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitSubscribeForm')) {

            $webFormDto = new WebFormDto(
                Tools::getValue('form', null),
                Tools::getValue('position', null),
                Tools::getValue('style', null),
                Tools::getValue('subscription', null)
            );

            $validator = new WebFormValidator($webFormDto);
            if (!$validator->isValid()) {
                $this->errors = $validator->getErrors();

                return;
            }

            $this->webFormService->updateWebForm($webFormDto);

            $this->confirmations[] = $webFormDto->isEnabled()
                ? $this->l('Form published')
                : $this->l('Form unpublished');

        }
        parent::postProcess();
    }

    public function renderForm()
    {
        $helper = new HelperForm();
        $helper->submit_action = 'submitSubscribeForm';
        $helper->token = Tools::getAdminTokenLite('AdminGetresponseSubscribeForm');
        $helper->tpl_vars = array(
            'fields_value' => $this->getFormFieldsValue()
        );

        $optionList = $this->getFormsOptions();

        return $helper->generateForm(array($this->getFormFields($optionList)));
    }

    /**
     * @return array
     */
    private function getFormFieldsValue()
    {
        $webForm = $this->webFormService->getWebForm();

        return [
            'position' => Tools::getValue('position', $webForm->getSidebar()),
            'form' => Tools::getValue('form', $webForm->getId()),
            'style' => Tools::getValue('style', $webForm->getStyle()),
            'subscription' => Tools::getValue(
                'subscription',
                $webForm->getStatus() === WebForm::STATUS_ACTIVE ? 1 : 0
            )
        ];
    }

    /**
     * @param WebFormCollection $webforms
     * @return array
     */
    private function getFormsOptions()
    {
        $options = [
            [
                'id_option' => '',
                'name' => 'Select a form you want to display'
            ]
        ];

        /** @var GetResponseForm $form */
        foreach ($this->getResponseWebFormCollection as $form) {
            $disabled = $form->isEnabled() ? '' : $this->l('(DISABLED IN GR)');
            $options[] = [
                'id_option' => $form->getWebFormId(),
                'name' => $form->getName() . ' (' . $form->getCampaignName() . ') ' . $disabled
            ];
        }

        return $options;
    }

    /**
     * @param array $options
     * @return array
     */
    private function getFormFields($options = [])
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Add Your GetResponse Forms (or Exit Popups) to Your Shop'),
                    'icon' => 'icon-gears'
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Add contacts to GetResponse via forms (or exit popups)'),
                        'name' => 'subscription',
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->l('Enabled')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->l('Disabled')]
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Form'),
                        'name' => 'form',
                        'required' => true,
                        'options' => [
                            'query' => $options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ]
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Block position'),
                        'name' => 'position',
                        'required' => true,
                        'options' => [
                            'query' => [
                                ['id_option' => '', 'name' => $this->l('Select where to place the form')],
                                ['id_option' => 'home', 'name' => $this->l('Homepage')],
                                ['id_option' => 'left', 'name' => $this->l('Left sidebar')],
                                ['id_option' => 'right', 'name' => $this->l('Right sidebar')],
                                ['id_option' => 'top', 'name' => $this->l('Top')],
                                ['id_option' => 'footer', 'name' => $this->l('Footer')],
                            ],
                            'id' => 'id_option',
                            'name' => 'name'
                        ]
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Style'),
                        'name' => 'style',
                        'required' => true,
                        'options' => [
                            'query' => [
                                ['id_option' => 'webform', 'name' => $this->l('Web Form')],
                                ['id_option' => 'prestashop', 'name' => 'Prestashop'],
                            ],
                            'id' => 'id_option',
                            'name' => 'name'
                        ]
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'saveWebForm',
                    'icon' => 'process-icon-save'
                ]
            ]
        ];
    }

}
