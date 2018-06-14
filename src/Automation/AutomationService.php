<?php
namespace GetResponse\Automation;

use GetResponse\Settings\Settings;
use GrShareCode\Campaign\AutorespondersCollection;
use GrShareCode\Campaign\CampaignsCollection;
use GrShareCode\Campaign\CampaignService;

/**
 * Class AutomationService
 */
class AutomationService
{
    /** @var AutomationRepository */
    private $automationRepository;
    /**
     * @var CampaignService
     */
    private $campaignService;

    /** @var Settings */
    private $settings;

    /**
     * @param AutomationRepository $automationRepository
     * @param CampaignService $campaignService
     * @param Settings $settings
     */
    public function __construct(
        AutomationRepository $automationRepository,
        CampaignService $campaignService,
        Settings $settings
    ) {
        $this->automationRepository = $automationRepository;
        $this->campaignService = $campaignService;
        $this->settings = $settings;
    }

    /**
     * @param int $automationId
     */
    public function deleteAutomationById($automationId)
    {
        $this->automationRepository->deleteAutomationSettings($automationId);
    }

    /**
     * @return Automation[]
     */
    public function getAutomation()
    {
        return $this->automationRepository->getAutomation();
    }

    /**
     * @return AutorespondersCollection
     */
    public function getAutoresponders()
    {
        return $this->campaignService->getAutoresponders();
    }

    /**
     * @return CampaignsCollection
     */
    public function getCampaigns()
    {
        return $this->campaignService->getAllCampaigns();
    }

    /**
     * @return int
     */
    public function getSettingsId()
    {
        return $this->settings->getId();
    }

    /**
     * @param int $categoryId
     * @param int $automationId
     * @param int $contactListId
     * @param string $action
     * @param int $cycleDay
     */
    public function updateAutomation($categoryId, $automationId, $contactListId, $action, $cycleDay)
    {
        $this->automationRepository->updateAutomation($categoryId, $automationId, $contactListId, $action, $cycleDay);
    }

    /**
     * @param int $categoryId
     * @param int $contactListId
     * @param string $action
     * @param int $cycleDay
     */
    public function addAutomation($categoryId, $contactListId, $action, $cycleDay)
    {
        $this->automationRepository->addAutomation($categoryId, $contactListId, $action, $cycleDay);
    }
}