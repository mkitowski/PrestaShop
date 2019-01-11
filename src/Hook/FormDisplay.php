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

namespace GetResponse\Hook;

use GetResponse\WebForm\WebFormService;

/**
 * Class FormDisplay
 * @package GetResponse\Hook
 */
class FormDisplay
{
    /** @var WebFormService */
    private $webFormService;

    /**
     * @param WebFormService $webFormService
     */
    public function __construct(WebFormService $webFormService)
    {
        $this->webFormService = $webFormService;
    }

    /**
     * @param string $position
     * @return array
     */
    public function displayWebForm($position)
    {
        if (empty($position)) {
            return [];
        }

        $webForm = $this->webFormService->getWebForm();

        if (!$webForm  || !$webForm->isActive() || !$webForm->hasSamePosition($position)) {
            return [];
        }

        $setStyle = $webForm->hasPrestashopStyle() ? '&css=1' : null;

        return [
            'webform_url' => $webForm->getUrl(),
            'style' => $setStyle,
            'position' => $position
        ];
    }
}
