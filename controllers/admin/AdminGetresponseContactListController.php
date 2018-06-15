<?php

use GetResponse\Automation\Automation;
use GetResponse\Automation\AutomationDto;
use GetResponse\Automation\AutomationListHelper;
use GetResponse\Automation\AutomationService;
use GetResponse\Automation\AutomationServiceFactory;
use GetResponse\Automation\AutomationValidator;
use GrShareCode\Campaign\Autoresponder;
use GrShareCode\Campaign\Campaign;

require_once 'AdminGetresponseController.php';

class AdminGetresponseContactListController extends AdminGetresponseController
{
    private $name = 'GRContactList';

    /**
     * @var AutomationService
     */
    private $automationService;

    public function __construct()
    {
        parent::__construct();

        $this->addJquery();
        $this->addJs(_MODULE_DIR_ . $this->module->name . '/views/js/gr-automation.js');

        $this->context->smarty->assign([
            'gr_tpl_path' => _PS_MODULE_DIR_ . 'getresponse/views/templates/admin/',
            'action_url' => $this->context->link->getAdminLink('AdminGetresponseContactList'),
            'base_url',
            __PS_BASE_URI__
        ]);

        $this->automationService = AutomationServiceFactory::create();
    }

    public function initPageHeaderToolbar()
    {
        if (!in_array($this->display, ['edit', 'add'])) {
            $this->page_header_toolbar_btn['new_rule'] = [
                'href' => self::$currentIndex . '&create' . $this->name . '&token=' . $this->getToken(),
                'desc' => $this->l('Add new rule', null, null, false),
                'icon' => 'process-icon-new'
            ];
        }
        parent::initPageHeaderToolbar();
    }

    /**
     * Get Admin Token
     * @return string
     */
    public function getToken()
    {
        return Tools::getAdminTokenLite('AdminGetresponseContactList');
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('GetResponse');
        $this->toolbar_title[] = $this->l('Contact List Rules');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('update' . $this->name)) {
            $this->display = 'edit';
        }

        if (Tools::isSubmit('create' . $this->name) || Tools::isSubmit('submit' . $this->name)) {
            $this->display = 'add';
        }

        if (Tools::isSubmit('delete' . $this->name)) {
            $this->automationService->deleteAutomationById(Tools::getValue('id'));
            $this->confirmations[] = $this->l('Rule deleted');
        }

        if (Tools::isSubmit('submitBulkdelete' . $this->name)) {
            $automationIdList = (array)Tools::getValue($this->name . 'Box');
            $this->automationService->deleteAutomationByIdList($automationIdList);
            $this->confirmations[] = $this->l('Rules deleted');
        }

        if (Tools::isSubmit('submit' . $this->name)) {

            $automationDto = new AutomationDto(
                Tools::getValue('id'),
                Tools::getValue('category'),
                Tools::getValue('campaign'),
                Tools::getValue('a_action'),
                Tools::getValue('options_1'),
                Tools::getValue('autoresponder_day')
            );

//            $id = Tools::getValue('id');
//            $category = Tools::getValue('category');
//            $campaign = Tools::getValue('campaign');
//            $action = Tools::getValue('a_action');
//            $addToCycle = Tools::getValue('options_1');
//            $cycleDay = !empty($addToCycle) ? Tools::getValue('autoresponder_day') : null;

            $validator = new AutomationValidator($automationDto);

            if (!$validator->isValid()) {
                $this->errors = $validator->getErrors();
                return;
            }

            if ($automationDto->hasId()) {
                try {
                    $this->automationService->updateAutomation($automationDto);
                } catch (Exception $e) {
                    $this->errors[] = $this->l('Rule has not been updated. Rule already exist.');
                }
            } else {
                try {
                    $this->automationService->addAutomation($automationDto);
                } catch (Exception $e) {
                    $this->errors[] = $this->l('Rule has not been created. Rule already exist.');
                }
            }

            if (empty($this->errors)) {
                Tools::redirectAdmin(AdminController::$currentIndex . '&token=' . $this->getToken());
            }
        }

        parent::postProcess();
    }

    /**
     * @return mixed
     */
    public function renderList()
    {
        $fieldsList = array(
            'category' => array('title' => $this->l('If customer buys in the category'), 'type' => 'text'),
            'action' => array('title' => $this->l('they are'), 'type' => 'text'),
            'contact_list' => array('title' => $this->l('Into the contact list'), 'type' => 'text'),
            'cycle_day' => array(
                'title' => $this->l('Add into the cycle on day'),
                'type' => 'bool',
                'icon' => array(
                    0 => 'disabled.gif',
                    1 => 'enabled.gif',
                    'default' => 'disabled.gif'
                ),
                'align' => 'center'
            ),
            'autoresponder' => array('title' => $this->l('Autoresponder'), 'type' => 'text'),
        );

        /** @var HelperListCore $helper */
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->identifier = 'id';
        $helper->actions = array('edit', 'delete');
        $helper->show_toolbar = false;
        $helper->title = $this->l('Contact List Rules');
        $helper->table = $this->name;
        $helper->token = $this->getToken();
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        $helper->bulk_actions = array(
            'delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?'))
        );

        $listHelper = AutomationListHelper::create(
            Category::getCategories(1, true, false),
            $this->automationService
        );

        return $helper->generateList($listHelper->getList(), $fieldsList);
    }

    /**
     * @return string
     */
    public function renderForm()
    {
        $id = Tools::getValue('id');

        $fieldsForm = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l(!empty($id) ? 'Edit rule' : 'Add new rule'),
                ),
                'input' => array(
                    array('type' => 'hidden', 'name' => 'automation_id'),
                    array('type' => 'hidden', 'name' => 'autoresponders'),
                    array('type' => 'hidden', 'name' => 'autoresponder_day_selected'),
                    array('type' => 'hidden', 'name' => 'cycle_day_selected'),
                    array(
                        'type' => 'select',
                        'label' => $this->l('If customer buys in category'),
                        'class' => 'gr-select',
                        'name' => 'category',
                        'required' => true,
                        'options' => array(
                            'query' => Category::getCategories(1, true, false),
                            'id' => 'id_category',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('They are'),
                        'class' => 'gr-select',
                        'name' => 'a_action',
                        'required' => true,
                        'options' => array(
                            'query' => array(
                                array('id' => '', 'name' => $this->l('Select from field')),
                                array('id' => 'move', 'name' => $this->l('Moved')),
                                array('id' => 'copy', 'name' => $this->l('Copied')),
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'class' => 'gr-select',
                        'label' => $this->l('Into the contact list'),
                        'name' => 'campaign',
                        'required' => true,
                        'options' => array(
                            'query' => array_merge(
                                array(array('id' => '', 'name' => $this->l('Select a list'))),
                                $this->getContactListForSelectField()
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'checkbox',
                        'label' => '',
                        'name' => 'options',
                        'values' => array(
                            'query' => array(array('id' => 1, 'name' => $this->l(' Add to autoresponder cycle'))),
                            'id' => 'id',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Autoresponder day'),
                        'class' => 'gr-select',
                        'name' => 'autoresponder_day',
                        'data-default' => $this->l('no autoresponders'),
                        'required' => true,
                        'options' => array(
                            'query' => array(array('id' => '', 'name' => $this->l('no autoresponders'))),
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'NewAutomationConfiguration'
                ),
                'reset' => array(
                    'title' => $this->l('Cancel'),
                    'icon' => 'process-icon-cancel'
                ),
                'show_cancel_button' => true,
            )
        );

        /** @var HelperFormCore $helper */
        $helper = new HelperForm();
        $helper->currentIndex = AdminController::$currentIndex;
        $helper->submit_action = 'submit' . $this->name;
        $helper->token = $this->getToken();
        $helper->fields_value = array(
            'category' => false,
            'a_action' => false,
            'campaign' => false,
            'autoresponder_day' => false,
            'autoresponder_day_selected' => false,
            'cycle_day_selected' => false,
            'automation_id' => false,
            'autoresponders' => json_encode($this->getAutoresponderField())
        );

        if (!empty($id)) {
            /** @var Automation $automation */
            foreach ($this->automationService->getAutomation() as $automation) {
                if ($automation->getId() === $id) {
                    $helper->fields_value['category'] = $automation->getCategoryId();
                    $helper->fields_value['a_action'] = $automation->getAction();
                    $helper->fields_value['campaign'] = $automation->getContactListId();
                    $helper->fields_value['autoresponder_day'] = $automation->getDayOfCycle();
                    $helper->fields_value['autoresponder_day_selected'] = $automation->getDayOfCycle();
                    $helper->fields_value['cycle_day_selected'] = !empty($automation->getDayOfCycle());
                    $helper->fields_value['automation_id'] = $id;
                    break;
                }
            }
        }

        return $helper->generateForm(array($fieldsForm));
    }

    /**
     * @return array
     */
    private function getContactListForSelectField()
    {
        $contactList = [];
        /** @var Campaign $campaign */
        foreach ($this->automationService->getCampaigns() as $campaign) {
            $contactList[] = [
                'id' => $campaign->getId(),
                'name' => $campaign->getName()
            ];
        }

        return $contactList;
    }

    /**
     * @return array
     */
    private function getAutoresponderField()
    {
        $autoresponders = [];

        /** @var Autoresponder $autoresponder */
        foreach ($this->automationService->getAutoresponders() as $autoresponder) {
            $autoresponders[] = [
                'campaignId' => $autoresponder->getCampaignId(),
                'dayOfCycle' => $autoresponder->getCycleDay(),
                'name' => $autoresponder->getName(),
                'subject' => $autoresponder->getSubject()
            ];
        }

        return $autoresponders;
    }
}
