<?php
namespace GetResponse\CustomFieldsMapping;

use Translate;

/**
 * Class CustomFieldMappingValidator
 * @package GetResponse\ContactList
 */
class CustomFieldMappingValidator
{
    /** @var array */
    private $errors;

    /** @var array */
    private $requestData;

    /**
     * @param array $requestData
     */
    public function __construct(array $requestData)
    {
        $this->requestData = $requestData;
        $this->errors = [];
        $this->validate();
    }

    private function validate()
    {
        if (preg_match('/^[\w\-]+$/', $this->requestData['name']) == false) {
            $this->errors[] = Translate::getAdminTranslation('Custom field contains invalid characters!');
        }

        if ($this->requestData['default'] == 1) {
            $this->errors[] = Translate::getAdminTranslation('Default mappings cannot be changed!');

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