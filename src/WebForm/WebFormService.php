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

namespace GetResponse\WebForm;

use GrShareCode\WebForm\WebFormCollection;
use GrShareCode\WebForm\WebFormService as GrWebFormService;
use GrShareCode\WebForm\FormNotFoundException;
use GrShareCode\Api\Exception\GetresponseApiException;

/**
 * Class WebFormService
 */
class WebFormService
{
    /** @var WebFormRepository */
    private $repository;

    /** @var GrWebFormService */
    private $grWebFormService;

    /**
     * @param WebFormRepository $repository
     * @param GrWebFormService $grWebFormService
     */
    public function __construct(WebFormRepository $repository, GrWebFormService $grWebFormService)
    {
        $this->repository = $repository;
        $this->grWebFormService = $grWebFormService;
    }

    /**
     * @param WebForm $webForm
     * @throws FormNotFoundException
     * @throws GetresponseApiException
     */
    public function updateWebForm(WebForm $webForm)
    {
        if ($webForm->isActive()) {
            $webForm->setUrl($this->getGetResponseFormCollection()->findOneById($webForm->getId())->getScriptUrl());
            $this->repository->update($webForm);
        } else {
            $this->repository->clearSettings();
        }
    }

    /**
     * @return WebForm
     */
    public function getWebForm()
    {
        return $this->repository->getWebForm();
    }

    /**
     * @return WebFormCollection
     * @throws GetresponseApiException
     */
    public function getGetResponseFormCollection()
    {
        return $this->grWebFormService->getAllWebForms();
    }
}
