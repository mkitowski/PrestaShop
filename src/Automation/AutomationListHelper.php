<?php
namespace GetResponse\Automation;

use GrShareCode\ContactList\Autoresponder;
use GrShareCode\ContactList\ContactList;
use Translate;

/**
 * Class AutomationListHelper
 * @package GetResponse\Automation
 */
class AutomationListHelper
{
    /** @var array */
    private $categories;

    /** @var AutomationService */
    private $automationService;

    /**
     * @param array $categories
     * @param AutomationService $automationService
     */
    public function __construct(array $categories, AutomationService $automationService)
    {
        $this->categories = $categories;
        $this->automationService = $automationService;
    }

    /**
     * @param array $categories
     * @param AutomationService $automationService
     * @return AutomationListHelper
     */
    public static function create(array $categories, AutomationService $automationService)
    {
        return new self($categories, $automationService);
    }

    /**
     * @return array
     */
    public function getList()
    {
        $automationList = [];

        /** @var Automation $automation */
        foreach ($this->automationService->getAutomation() as $automation) {
            $automationList[] = array(
                'id' => $automation->getId(),
                'category' => $this->getCategoryNameById($automation->getCategoryId()),
                'action' => Translate::getAdminTranslation($automation->getAction()),
                'contact_list' => $this->getContactListNameById($automation->getContactListId()),
                'cycle_day' => is_numeric($automation->getDayOfCycle()),
                'autoresponder' => $this->getAutoresponderName($automation->getDayOfCycle()),
            );
        }

        return $automationList;
    }

    /**
     * @param int $categoryId
     * @return string
     */
    private function getCategoryNameById($categoryId)
    {
        foreach ($this->categories as $category) {
            if ($category['id_category'] === $categoryId) {
                return $category['name'];
            }
        }

        return '';
    }

    /**
     * @param string $campaignId
     * @return string
     */
    private function getContactListNameById($campaignId)
    {
        /** @var ContactList $contactList */
        foreach ($this->automationService->getContactLists() as $contactList) {
            if ($contactList->getId() === $campaignId) {
                return $contactList->getName();
            }
        }

        return '';
    }

    /**
     * @param int $cycleDay
     * @return string
     */
    private function getAutoresponderName($cycleDay)
    {
        /** @var Autoresponder $autoresponder */
        foreach ($this->automationService->getAutoresponders() as $autoresponder) {
            if ($autoresponder->getCycleDay() === $cycleDay) {
                return '(' . Translate::getAdminTranslation('Day') . ': '
                    . $autoresponder->getCycleDay() . ') '
                    . $autoresponder->getName() . ' ('
                    . Translate::getAdminTranslation('Subject') . ': '
                    . $autoresponder->getSubject() . ')';
            }
        }

        return '';
    }

}