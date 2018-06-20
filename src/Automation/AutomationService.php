<?php
namespace GetResponse\Automation;

use GetResponse\Account\AccountSettings;
use GetResponse\ContactList\ContactListService;
use GrShareCode\ContactList\ContactListCollection;

/**
 * Class AutomationService
 */
class AutomationService
{
    /** @var AutomationRepository */
    private $automationRepository;

    /** @var ContactListService */
    private $contactListService;

    /** @var AccountSettings */
    private $settings;

    /**
     * @param AutomationRepository $automationRepository
     * @param ContactListService $contactListService
     * @param AccountSettings $settings
     */
    public function __construct(
        AutomationRepository $automationRepository,
        ContactListService $contactListService,
        AccountSettings $settings
    ) {
        $this->automationRepository = $automationRepository;
        $this->contactListService = $contactListService;
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
     * @return \GrShareCode\ContactList\AutorespondersCollection
     */
    public function getAutoresponders()
    {
        return $this->contactListService->getAutoresponders();
    }

    /**
     * @return ContactListCollection
     */
    public function getContactLists()
    {
        return $this->contactListService->getContactLists();
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