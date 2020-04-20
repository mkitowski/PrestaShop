<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author     Getresponse <grintegrations@getresponse.com>
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace GetResponse\Config;

/**
 * Class Config
 * @package GetResponse\Config
 */
class ConfigService
{
    const X_APP_ID = '2cd8a6dc-003f-4bc3-ba55-c2e4be6f7500';

    const USED_HOOKS = [
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
        'displayBackOfficeHeader'
    ];

    const BACKOFFICE_TABS = [
        [
            'class_name' => 'AdminGetresponseAccount',
            'name' => 'GetResponse Account',
        ],
        [
            'class_name' => 'AdminGetresponseExport',
            'name' => 'Export Customer Data',
        ],
        [
            'class_name' => 'AdminGetresponseSubscribeRegistration',
            'name' => 'Subscribe via Registration',
        ],
        [
            'class_name' => 'AdminGetresponseAddNewContactList',
            'name' => 'Add new contact list',
            'parent' => -1
        ],
        [
            'class_name' => 'AdminGetresponseUpdateMapping',
            'name' => 'Update mapping',
            'parent' => -1
        ],
        [
            'class_name' => 'AdminGetresponseSubscribeForm',
            'name' => 'Subscribe via Forms',
        ],
        [
            'class_name' => 'AdminGetresponseWebTracking',
            'name' => 'Web Event Tracking',
        ],
        [
            'class_name' => 'AdminGetresponseEcommerce',
            'name' => 'GetResponse Ecommerce',
        ],
    ];

    const INSTALLED_CLASSES = [
        'AdminGetresponseExport',
        'AdminGetresponseSubscribeRegistration',
        'AdminGetresponseAddNewContactList',
        'AdminGetresponseUpdateMapping',
        'AdminGetresponseSubscribeForm',
        'AdminGetresponseContactListRule',
        'AdminGetresponseWebTracking',
        'AdminGetresponseEcommerce',
        'AdminGetresponseAccount',
        'AdminGetresponseAddNewContactList',
        'AdminGetresponse',
        'Getresponse'
    ];

    const CONFIRM_UNINSTALL = 'Warning: all the module data will be deleted. 
    Are you sure you want uninstall this module?';
}
