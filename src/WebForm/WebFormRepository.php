<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace GetResponse\WebForm;

use Configuration;

/**
 * Class WebFormRepository
 */
class WebFormRepository
{
    const RESOURCE_KEY = 'getresponse_forms';

    /**
     * @param WebForm $webForm
     */
    public function update(WebForm $webForm)
    {
        Configuration::updateValue(
            self::RESOURCE_KEY,
            json_encode([
                'status' => $webForm->getStatus(),
                'webform_id' => $webForm->getId(),
                'sidebar' => $webForm->getSidebar(),
                'style' => $webForm->getStyle(),
                'url' => $webForm->getUrl()
            ])
        );
    }

    /**
     * @return WebForm|null
     */
    public function getWebForm()
    {
        $result = json_decode(Configuration::get(self::RESOURCE_KEY), true);

        if (empty($result)) {
            return WebForm::createEmptyInstance();
        }

        return new WebForm(
            $result['status'],
            $result['webform_id'],
            $result['sidebar'],
            $result['style'],
            $result['url']
        );
    }

    public function clearSettings()
    {
        Configuration::deleteByName(self::RESOURCE_KEY);
    }
}
