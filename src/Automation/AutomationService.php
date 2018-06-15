<?php
namespace GetResponse\Automation;

use GetResponse\Account\AccountSettings;
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

    /** @var CampaignService */
    private $campaignService;

    /** @var AccountSettings */
    private $settings;

    /**
     * @param AutomationRepository $automationRepository
     * @param CampaignService $campaignService
     * @param AccountSettings $settings
     */
    public function __construct(
        AutomationRepository $automationRepository,
        CampaignService $campaignService,
        AccountSettings $settings
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
     * @param array $automationIds
     */
    public function deleteAutomationByIdList(array $automationIds)
    {
        foreach ($automationIds as $automationId) {
            $this->automationRepository->deleteAutomationSettings($automationId);
        }
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
     * @param AutomationDto $automationDto
     */
    public function updateAutomation(AutomationDto $automationDto)
    {
        $this->automationRepository->updateAutomation(
            $automationDto->getCategory(),
            $automationDto->getId(),
            $automationDto->getContactListId(),
            $automationDto->getAction(),
            $automationDto->getCycleDay()
        );
    }

    /**
     * @param AutomationDto $automationDto
     */
    public function addAutomation(AutomationDto $automationDto)
    {
        $this->automationRepository->addAutomation(
            $automationDto->getCategory(),
            $automationDto->getContactListId(),
            $automationDto->getAction(),
            $automationDto->getCycleDay()
        );
    }
}