<?php
namespace GetResponse\Export;

use Translate;

/**
 * Class ExportValidator
 * @package GetResponse\Export
 */
class ExportValidator
{
    /** @var array */
    private $errors;

    /** @var ExportSettings */
    private $exportSettings;

    /**
     * @param ExportSettings $exportSettings
     */
    public function __construct(ExportSettings $exportSettings)
    {
        $this->exportSettings = $exportSettings;
        $this->errors = [];
        $this->validate();
    }

    private function validate()
    {
        if (empty($this->exportSettings->getContactListId())) {
            $this->errors[] = Translate::getAdminTranslation('You need to select list');
            return;
        }

        if ($this->exportSettings->isEcommerce() && empty($this->exportSettings->getShopId())) {
            $this->errors[] = Translate::getAdminTranslation('You need to select store');
            return;
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