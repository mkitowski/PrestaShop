<?php
namespace GetResponse\WebForm;

use GrShareCode\WebForm\WebFormCollection;
use GrShareCode\WebForm\WebFormService as GrWebFormService;

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
     */
    public function updateWebForm(WebForm $webForm)
    {
        $this->repository->update($webForm);
    }

    /**
     * @return null|WebForm
     */
    public function getWebForm()
    {
        return $this->repository->getWebForm();
    }

    /**
     * @param string $subscription
     */
    public function updateWebFormSubscription($subscription)
    {
        $this->repository->updateWebFormSubscription($subscription);
    }

    /**
     * @return WebFormCollection
     */
    public function getGetResponseFormCollection()
    {
        return $this->grWebFormService->getAllWebForms();
    }
}