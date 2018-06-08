<?php
namespace GetResponse\Config;

use GetResponseRepository;

/**
 * Class Config
 * @package GetResponse\Config
 */
class ConfigService
{
    const X_APP_ID = '2cd8a6dc-003f-4bc3-ba55-c2e4be6f7500';

    const USED_HOOKS = array(
        'newOrder',
        'createAccount',
        'leftColumn',
        'rightColumn',
        'header',
        'footer',
        'top',
        'home',
        'cart',
        'postUpdateOrderStatus',
        'hookOrderConfirmation',
        'displayBackOfficeHeader',
        'actionCronJob'
    );

    const BACKOFFICE_TABS = array(
        array(
            'class_name' => 'AdminGetresponseAccount',
            'name' => 'GetResponse Account',
        ),
        array(
            'class_name' => 'AdminGetresponseExport',
            'name' => 'Export Customer Data',
        ),
        array(
            'class_name' => 'AdminGetresponseSubscribeRegistration',
            'name' => 'Subscribe via Registration',
        ),
        array(
            'class_name' => 'AdminGetresponseSubscribeForm',
            'name' => 'Subscribe via Forms',
        ),
        array(
            'class_name' => 'AdminGetresponseContactList',
            'name' => 'Contact List Rules',
        ),
        array(
            'class_name' => 'AdminGetresponseWebTracking',
            'name' => 'Web Event Tracking',
        ),
        array(
            'class_name' => 'AdminGetresponseEcommerce',
            'name' => 'GetResponse Ecommerce',
        ),
    );

    const INSTALLED_CLASSES = array(
        'AdminGetresponseExport',
        'AdminGetresponseSubscribeRegistration',
        'AdminGetresponseSubscribeForm',
        'AdminGetresponseContactList',
        'AdminGetresponseWebTracking',
        'AdminGetresponseEcommerce',
        'AdminGetresponseAccount',
        'AdminGetresponse',
        'Getresponse'
    );

    const CONFIRM_UNINSTALL = 'Warning: all the module data will be deleted. Are you sure you want uninstall this module?';

    const MODULE_DESCRIPTION = '
            Add your Prestashop contacts to GetResponse or manage them via automation rules.
            Automatically follow-up new subscriptions with engaging email marketing campaigns
            ';

    /** @var GetResponseRepository */
    private $repository;

    public function __construct(GetResponseRepository $repository)
    {
        $this->repository = $repository;
    }
}