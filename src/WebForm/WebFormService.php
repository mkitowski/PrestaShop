<?php
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
