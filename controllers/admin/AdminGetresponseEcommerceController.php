<?php

use GetResponse\Ecommerce\Activity;
use GetResponse\Ecommerce\EcommerceService;
use GetResponse\Ecommerce\EcommerceServiceFactory;
use GrShareCode\Shop\AddShopCommand;
use GrShareCode\Shop\Shop;

require_once 'AdminGetresponseController.php';

class AdminGetresponseEcommerceController extends AdminGetresponseController
{
    private $name = 'GREcommerce';

    /** @var EcommerceService */
    private $ecommerceService;

    public function __construct()
    {
        parent::__construct();
        $this->addJquery();
        $this->addJs(_MODULE_DIR_ . $this->module->name . '/views/js/gr-ecommerce.js');
        $this->ecommerceService = EcommerceServiceFactory::create();
    }

    public function initPageHeaderToolbar()
    {
        if (!in_array($this->display, array('edit', 'add'))) {
            $this->page_header_toolbar_btn['new_shop'] = array(
                'href' => self::$currentIndex . '&action=add&token=' . $this->getToken(),
                'desc' => $this->l('Add new shop', null, null, false),
                'icon' => 'process-icon-new'
            );
        }
        parent::initPageHeaderToolbar();
    }

    /**
     * Get Admin Token
     * @return string
     */
    public function getToken()
    {
        return Tools::getAdminTokenLite('AdminGetresponseEcommerce');
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('GetResponse');
        $this->toolbar_title[] = $this->l('GetResponse Ecommerce');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('delete' . $this->name)) {
            $this->ecommerceService->deleteShop(Tools::getValue('shopId'));
            $this->confirmations[] = $this->l('Ecommerce settings saved');
        }

        if (Tools::isSubmit('submit' . $this->name) && Tools::getValue('ecommerce') !== false) {

            $grShopId = Tools::getValue('shop');
            $activity = Activity::createFromRequest(Tools::getValue('ecommerce'));

            if ($activity->isEnabled() && empty($grShopId)) {
                $this->errors[] = $this->l('You need to select shop');

                return;
            }

            if (!$this->ecommerceService->isSubscribeViaRegistrationActive()) {
                $this->errors[] = $this->l(
                    'You need to enable adding contacts during registrations to enable ecommerce'
                );

                return;
            }

            $this->ecommerceService->updateEcommerceDetails($grShopId, $activity);
            $this->confirmations[] = $this->l('Ecommerce settings saved');
        }

        if (Tools::getValue('action') == 'add') {

            $this->display = 'add';

            if (Tools::isSubmit('submit' . $this->name) && Tools::getValue('form_name') == 'add_store') {

                $shopName = Tools::getValue('shop_name');

                if (empty($shopName)) {
                    $this->errors[] = $this->l('Store name can not be empty');

                    return;
                }

                $this->ecommerceService->createShop(
                    new AddShopCommand(
                        $shopName,
                        $this->context->language->iso_code,
                        $this->context->currency->iso_code
                    )
                );
                $this->display = 'list';
                $this->confirmations[] = $this->l('Store added');
            }
        }

        parent::postProcess();
    }

    /**
     * @return string
     */
    public function renderList()
    {
        return $this->generateForm() . $this->generateShopList();
    }

    /**
     * @return string
     */
    private function generateForm()
    {
        $shops[] = array('shopId' => '', 'name' => $this->l('Select a shop'));

        /** @var Shop $shop */
        foreach ($this->ecommerceService->getAllShops() as $shop) {
            $shops[] = [
                'shopId' => $shop->getId(),
                'name' => $shop->getName(),
            ];
        }

        $fieldsForm = [
            'form' => [
                'legend' => [
                    'title' => $this->l((!$this->ecommerceService->isEcommerceEnabled() ? 'Enable ' : '') . 'GetResponse Ecommerce')
                ],
                'description' =>
                    $this->l(
                        'GetResponse helps you track and collect ecommerce data. 
                        You can stay informed about customers’ behaviour and spending habits.'
                    ) . '<br>' .
                    $this->l(
                        'Use this data to create marketing automation workflows that react to 
                        purchases, abandoned carts, or the amounts of money spent.'
                    ) . '<br>' .
                    $this->l(
                        'Make sure to <u>enable adding contacts during registration</u> to 
                        start sending ecommerce data to GetResponse.',
                        false,
                        false,
                        false
                    ),
                'input' => [
                    [
                        'label' => $this->l('Send ecommerce data to GetResponse'),
                        'name' => 'ecommerce',
                        'type' => 'switch',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'ecommerce_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ],
                            [
                                'id' => 'ecommerce_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            ]
                        ]
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Shop'),
                        'class' => 'gr-select',
                        'name' => 'shop',
                        'required' => true,
                        'options' => [
                            'query' => $shops,
                            'id' => 'shopId',
                            'name' => 'name'
                        ]
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'EcommerceConfiguration'
                ]
            ]
        ];

        /** @var HelperFormCore $helper */
        $helper = new HelperForm();
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->submit_action = 'submit' . $this->name;
        $helper->token = $this->getToken();
        $helper->title = $this->l('Enable GetResponse Ecommerce');
        $helper->fields_value = array('ecommerce' => 0, 'shop' => '');
        $settings = $this->ecommerceService->getEcommerceSettings();
        $activity = Activity::createFromRequest(Tools::getValue('ecommerce'));

        if ($settings !== null) {
            $helper->fields_value = [
                'ecommerce' => 1,
                'shop' => $settings->getGetResponseShopId()
            ];
        } elseif (Tools::isSubmit('submit' . $this->name) && $activity->isEnabled()) {
            $helper->fields_value = [
                'ecommerce' => 1,
                'shop' => Tools::getValue('shop')
            ];
        }

        return $helper->generateForm(array($fieldsForm));
    }

    /**
     * @return string
     */
    private function generateShopList()
    {
        /** @var HelperListCore $helper */
        $helper = new HelperList();
        $helper->no_link = true;
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->identifier = 'shopId';
        $helper->actions = array('delete');
        $helper->title = $this->l('Stores');
        $helper->table = $this->name;
        $helper->token = $this->getToken();
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        $fieldsList = [
            'shopId' => [
                'title' => $this->l('ID'),
                'type' => 'text'
            ],
            'name' => [
                'title' => $this->l('Shop name'),
                'type' => 'text'
            ]
        ];

        $shopList = [];

        /** @var Shop $shop */
        foreach ($this->ecommerceService->getAllShops() as $shop) {
            $shopList[] = [
                'shopId' => $shop->getId(),
                'name' => $shop->getName(),
            ];
        }

        return $helper->generateList($shopList, $fieldsList);
    }

    /**
     * @return string
     */
    public function renderForm()
    {
        $fieldsForm = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Add new store'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Store name'),
                        'required' => true,
                        'name' => 'shop_name',
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'form_name'
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'back_url'
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'NewAutomationConfiguration'
                ),
                'reset' => array(
                    'title' => $this->l('Cancel'),
                    'icon' => 'process-icon-cancel'
                ),
                'show_cancel_button' => true
            )
        );

        /** @var HelperFormCore $helper */
        $helper = new HelperForm();
        $helper->currentIndex = AdminController::$currentIndex . '&action=add';
        $helper->submit_action = 'submit' . $this->name;
        $helper->token = $this->getToken();

        $helper->fields_value = array(
            'shop_name' => '',
            'form_name' => 'add_store',
            'back_url' => self::$currentIndex . '&token=' . $this->getToken(),
        );

        return $helper->generateForm(array($fieldsForm));
    }

}
