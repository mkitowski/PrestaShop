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

    /** @var WebForm */
    private $webForm;

    /**
     * @param WebForm $webForm
     */
    public function __construct(WebForm $webForm)
    {
        $this->webForm = $webForm;
        $this->errors = [];
        $this->validate();
    }

    private function validate()
    {
        if ($this->webForm->isActive()) {
            if (empty($this->webForm->getId()) || empty($this->webForm->getSidebar())) {
                $this->errors[] = Translate::getAdminTranslation('You need to select a form and its placement');
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
