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
     * @param WebFormDto $webFormDto
     * @throws FormNotFoundException
     * @throws GetresponseApiException
     */
    public function updateWebForm(WebFormDto $webFormDto)
    {
        $webFormUrl = $webFormDto->isEnabled()
            ? $this->getGetResponseFormCollection()->findOneById($webFormDto->getFormId())->getScriptUrl()
            : '';

        $webForm = new WebForm(
            $webFormDto->getFormId(),
            $webFormDto->getStatus(),
            empty($webFormDto->getPosition()) ? WebForm::SIDEBAR_DEFAULT : $webFormDto->getPosition(),
            empty($webFormDto->getStyle()) ? WebForm::STYLE_DEFAULT : $webFormDto->getStyle(),
            $webFormUrl
        );
        $this->repository->update($webForm);
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
