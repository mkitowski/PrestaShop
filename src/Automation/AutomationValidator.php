<?php
namespace GetResponse\Automation;

use Translate;

/**
 * Class AutomationValidator
 * @package GetResponse\Automation
 */
class AutomationValidator
{
    /** @var array */
    private $errors;

    /** @var AutomationDto */
    private $automationDto;

    /**
     * @param AutomationDto $automationDto
     */
    public function __construct(AutomationDto $automationDto)
    {
        $this->automationDto = $automationDto;
        $this->errors = [];
        $this->validate();
    }

    private function validate()
    {
        if (empty($this->automationDto->getCategory())) {
            $this->errors[] = Translate::getAdminTranslation('The "if customer buys in category field" is invalid');
        }
        if (empty($this->automationDto->getAction())) {
            $this->errors[] = Translate::getAdminTranslation('The "they are" field is required');
        }
        if (empty($this->automationDto->getContactListId())) {
            $this->errors[] = Translate::getAdminTranslation('The "into the contact list" field is required');
        }

        if (!empty($this->automationDto->getAddToCycle()) && $this->automationDto->getCycleDay() === '') {
            $this->errors[] = Translate::getAdminTranslation('The "autoresponder" field is required');
        }
        return empty($this->errors);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}