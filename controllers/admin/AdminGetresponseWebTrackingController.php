<?php

use GetResponse\WebTracking\WebTracking;
use GetResponse\WebTracking\WebTrackingService;
use GetResponse\WebTracking\WebTrackingServiceFactory;
use GrShareCode\Api\Authorization\ApiTypeException;
use GrShareCode\Api\Exception\GetresponseApiException;

require_once 'AdminGetresponseController.php';

class AdminGetresponseWebTrackingController extends AdminGetresponseController
{
    /** @var WebTrackingService */
    private $webTrackingService;

    /**
     * @throws PrestaShopException
     * @throws ApiTypeException
     */
    public function __construct()
    {
        parent::__construct();
        $this->webTrackingService = WebTrackingServiceFactory::create();
    }

    public function initContent()
    {
        $this->display = 'edit';
        $this->show_form_cancel_button = false;
        $this->toolbar_title[] = $this->l('Administration');
        $this->toolbar_title[] = $this->l('Web Event Trackinge');

        parent::initContent();
    }

    /**
     * @return bool|ObjectModel|void
     * @throws GetresponseApiException
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitWebTrackingForm')) {

            $status = (int) Tools::getValue('tracking');

            $this->webTrackingService->saveTracking(
                $status === 1 ?
                WebTracking::TRACKING_ACTIVE
                : WebTracking::TRACKING_INACTIVE
            );

            $this->confirmations[] = $status === WebTracking::TRACKING_ACTIVE
                ? $this->l('Web event traffic tracking enabled')
                : $this->l('Web event traffic tracking disabled');
        }

        parent::postProcess();
    }

    /**
     * @return string
     */
    public function renderForm()
    {
        $webTracking = $this->webTrackingService->getWebTracking();

        $helper = new HelperForm();
        $helper->submit_action = 'submitWebTrackingForm';
        $helper->token = Tools::getAdminTokenLite('AdminGetresponseWebTrackingForm');

        if ($webTracking !== null && !$webTracking->isTrackingDisabled()) {
            $helper->tpl_vars = ['fields_value' => ['tracking' => $webTracking->isTrackingActive()]];
            $fields_form = $this->getFormForTrackingEnabled();
        } else {
            $fields_form = $this->getFormFieldsForTrackingDisabled();
        }

        return $helper->generateForm([$fields_form]);
    }

    /**
     * @return array
     */
    private function getFormForTrackingEnabled()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Web Event Tracking'),
                ],
                'description' => $this->l('
                    Enable event tracking in GetResponse to uncover who is visiting your stores, 
                    how often, and why. Analyze and react to customer buying habits.
                '),
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Send web event data to GetResponse'),
                        'name' => 'tracking',
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'active_on', 'value' => WebTracking::TRACKING_ON, 'label' => $this->l('Yes')],
                            ['id' => 'active_off', 'value' => WebTracking::TRACKING_OFF, 'label' => $this->l('No')]
                        ],
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submitTracking',
                    'icon' => 'process-icon-save'
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    private function getFormFieldsForTrackingDisabled()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Web Event Tracking'),
                ],
                'description' =>
                    $this->l('
                        We can’t start sending data from PrestaShop to GetResponse yet. 
                        Make sure you have a Max or Pro account.
                    ') . '<br>' .
                    $this->l('
                        If you have a Max or Pro account, try disconnecting and reconnecting 
                        the GetResponse account within the GetResponse module. This should correct the issue.
                    ')
            ]
        ];
    }

}
