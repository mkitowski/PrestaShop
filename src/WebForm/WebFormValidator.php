<?php
namespace GetResponse\WebForm;

use Translate;

/**
 * Class WebFormValidator
 * @package GetResponse\WebForm
 */
class WebFormValidator
{
    /** @var array */
    private $errors;

    /** @var WebFormDto */
    private $webFormDto;

    /**
     * @param WebFormDto $webFormDto
     */
    public function __construct(WebFormDto $webFormDto)
    {
        $this->webFormDto = $webFormDto;
        $this->errors = [];
        $this->validate();
    }

    private function validate()
    {
        if ($this->webFormDto->isEnabled()) {
            if (empty($this->webFormDto->getFormId()) || empty($this->webFormDto->getPosition())) {
                $this->errors[] = Translate::getAdminTranslation('You need to select a form and its placement');

                return;
            }
        }
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