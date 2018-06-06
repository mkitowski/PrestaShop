<?php

use GrShareCode\WebForm\WebForm;
use GrShareCode\WebForm\WebFormCollection;
use GrShareCode\WebForm\WebFormService;

require_once 'AdminGetresponseController.php';

/**
 * Class AdminGetresponseController
 *
 * @author Getresponse <grintegrations@getresponse.com>
 * @copyright GetResponse
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class AdminGetresponseSubscribeFormController extends AdminGetresponseController
{
    public function __construct()
    {
        parent::__construct();
        $this->addJquery();
        $this->addJs(_MODULE_DIR_ . $this->module->name . '/views/js/gr-webform.js');

        $this->context->smarty->assign(array(
            'gr_tpl_path' => _PS_MODULE_DIR_ . 'getresponse/views/templates/admin/',
            'action_url' => $this->context->link->getAdminLink('AdminGetresponseSubscribeForm'),
            'base_url' => __PS_BASE_URI__
        ));
    }

    public function initContent()
    {
        $this->display = 'edit';
        $this->show_form_cancel_button = false;

        parent::initContent();
    }


    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('GetResponse');
        $this->toolbar_title[] = $this->l('Add Contacts via GetResponse Forms');
    }

    /**
     * render main view
     * @return string
     */
    public function renderForm()
    {
        $this->show_form_cancel_button = false;
        $settings = $this->repository->getSettings();
        $isConnected = !empty($settings['api_key']) ? true : false;

        $this->context->smarty->assign(array(
            'is_connected' => $isConnected,
            'active_tracking' => $settings['active_tracking']
        ));

        if (false === $isConnected) {
            $this->apiView();

            return parent::renderView();
        }

        $subscription = Tools::getValue('subscription', null);

        if ($subscription != null) {
            $this->performSubscribeViaForm();
        }

        return $this->subscribeViaFormView();
    }

    public function performSubscribeViaForm()
    {
        $this->redirectIfNotAuthorized();
        $webFormId = Tools::getValue('form', null);
        $webFormSidebar = Tools::getValue('position', null);
        $webFormStyle = Tools::getValue('style', null);
        $subscription = Tools::getValue('subscription', null);

        $this->repository->updateWebformSubscription($subscription === '1' ? 'yes' : 'no');

        if (empty($webFormId) || empty($webFormSidebar)) {
            $this->errors[] = $this->l('You need to select a form and its placement');

            return;
        }

        $dbSettings = $this->repository->getSettings();
        $api = GrApiFactory::createFromSettings($dbSettings);
        $formService = new WebFormService($api);
        $webForms = $formService->getAllWebForms();
        $mergedWebForms = array();

        /** @var WebForm $form */
        foreach ($webForms as $form) {
            $mergedWebForms[$form->getWebFormId()] = $form->getScriptUrl();
        }

        // set web form info to DB
        $this->repository->updateWebformSettings(
            $webFormId,
            $subscription === '1' ? 'yes' : 'no',
            $webFormSidebar,
            $webFormStyle,
            $mergedWebForms[$webFormId]
        );
        if ($subscription) {
            $this->confirmations[] = $this->l('Form published');
        } else {
            $this->confirmations[] = $this->l('Form unpublished');
        }

        $this->subscribeViaFormView();
    }

    /**
     * Subscription via webform
     */
    public function subscribeViaFormView()
    {
        $this->redirectIfNotAuthorized();

        $this->context->smarty->assign(array('selected_tab' => 'subscribe_via_form'));

        $dbSettings = $this->repository->getSettings();
        $api = GrApiFactory::createFromSettings($dbSettings);
        $formService = new WebFormService($api);
        $webforms = $formService->getAllWebForms();

        $options = $this->convertFormsToDisplayArray($webforms);

        return $this->renderSubscribeForm($options);
    }

    /**
     * @param WebFormCollection $webforms
     * @return array
     */
    public function convertFormsToDisplayArray(WebFormCollection $webforms)
    {
        $options = array(
            array(
                'id_option' => '',
                'name' => 'Select a form you want to display'
            )
        );

        /** @var WebForm $form */
        foreach ($webforms as $form) {
            $disabled = $form->isEnabled() ? '' : $this->l('(DISABLED IN GR)');
            $options[] = array(
                'id_option' => $form->getWebFormId(),
                'name' => $form->getName() . ' (' . $form->getCampaignName() . ') ' . $disabled
            );
        }

        return $options;
    }

    public function renderSubscribeForm($forms = [])
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Add Your GetResponse Forms (or Exit Popups) to Your Shop'),
                'icon' => 'icon-gears'
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Add contacts to GetResponse via forms (or exit popups)'),
                    'name' => 'subscription',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Enabled')),
                        array('id' => 'active_off', 'value' => 0, 'label' => $this->l('Disabled'))
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Form'),
                    'name' => 'form',
                    'required' => true,
                    'options' => array(
                        'query' => $forms,
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Block position'),
                    'name' => 'position',
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array('id_option' => '', 'name' => $this->l('Select where to place the form')),
                            array('id_option' => 'home', 'name' => $this->l('Homepage')),
                            array('id_option' => 'left', 'name' => $this->l('Left sidebar')),
                            array('id_option' => 'right', 'name' => $this->l('Right sidebar')),
                            array('id_option' => 'top', 'name' => $this->l('Top')),
                            array('id_option' => 'footer', 'name' => $this->l('Footer')),
                        ),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Style'),
                    'name' => 'style',
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array('id_option' => 'webform', 'name' => $this->l('Web Form')),
                            array('id_option' => 'prestashop', 'name' => 'Prestashop'),
                        ),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'saveWebForm',
                'icon' => 'process-icon-save'
            )
        );

        return parent::renderForm();
    }

    public function getFieldsValue($obj)
    {
        $webformSettings = $this->repository->getWebformSettings();

        return array(
            'position' => $webformSettings['sidebar'],
            'form' => $webformSettings['webform_id'],
            'style' => $webformSettings['style'],
            'subscription' => $webformSettings['active_subscription'] === 'yes' ? 1 : 0
        );
    }

    /**
     * Get Admin Token
     * @return bool|string
     */
    public function getToken()
    {
        return Tools::getAdminTokenLite('AdminGetresponseSubscribeForm');
    }
}
