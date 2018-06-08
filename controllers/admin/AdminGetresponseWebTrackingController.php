<?php

use GetResponse\Settings\SettingsService;
use GetResponse\Settings\SettingsServiceFactory;
use GetResponse\WebTracking\WebTracking;
use GrShareCode\TrackingCode\TrackingCodeService;

require_once 'AdminGetresponseController.php';

class AdminGetresponseWebTrackingController extends AdminGetresponseController
{
    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct()
    {
        parent::__construct();
        $this->settingsService = SettingsServiceFactory::create();
    }

    public function initContent()
    {
        $this->display = 'edit';
        $this->show_form_cancel_button = false;
        $this->toolbar_title[] = $this->l('Administration');
        $this->toolbar_title[] = $this->l('Web Event Trackinge');

        parent::initContent();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitWebTrackingForm')) {

            $snippet = '';

            $tracking = new WebTracking(Tools::getValue('tracking'));

            if ($tracking->isEnabled()) {

                $trackingCodeService = new TrackingCodeService($this->getGrAPI());
                $trackingCode = $trackingCodeService->getTrackingCode();
                $snippet = $trackingCode->getSnippet();
                $this->confirmations[] = $this->l('Web event traffic tracking enabled');

            } elseif ($tracking->isDisabled()) {

                $this->confirmations[] = $this->l('Web event traffic tracking disabled');
            }

            $this->settingsService->updateTracking($tracking->toSettings(), $snippet);

        }
        parent::postProcess();
    }

    /**
     * @return string
     */
    public function renderForm()
    {
        $settings = $this->settingsService->getSettings();

        $helper = new HelperForm();
        $helper->submit_action = 'submitWebTrackingForm';
        $helper->token = Tools::getAdminTokenLite('AdminGetresponseWebTrackingForm');

        if (!$settings->isTrackingDisabled()) {
            $helper->tpl_vars = array(
                'fields_value' => array('tracking' => $settings->isTrackingActive())
            );
            $fields_form = $this->getFormForTrackingEnabled();
        } else {
            $fields_form = $this->getFormFieldsForTrackingDisabled();
        }

        return $helper->generateForm(array($fields_form));
    }

    /**
     * @return array
     */
    private function getFormForTrackingEnabled()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Web Event Tracking'),
                ),
                'description' => $this->l('
                    Enable event tracking in GetResponse to uncover who is visiting your stores, 
                    how often, and why. Analyze and react to customer buying habits.
                '),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Send web event data to GetResponse'),
                        'name' => 'tracking',
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array('id' => 'active_on', 'value' => WebTracking::TRACKING_ON, 'label' => $this->l('Yes')),
                            array('id' => 'active_off', 'value' => WebTracking::TRACKING_OFF, 'label' => $this->l('No'))
                        ),
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submitTracking',
                    'icon' => 'process-icon-save'
                )
            )
        );
    }

    /**
     * @return array
     */
    private function getFormFieldsForTrackingDisabled()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Web Event Tracking'),
                ),
                'description' =>
                    $this->l('
                        We can’t start sending data from PrestaShop to GetResponse yet. 
                        Make sure you have a Max or Pro account.
                    ') . '<br>' .
                    $this->l('
                        If you have a Max or Pro account, try disconnecting and reconnecting 
                        the GetResponse account within the GetResponse module. This should correct the issue.
                    ')
            )
        );
    }

}
