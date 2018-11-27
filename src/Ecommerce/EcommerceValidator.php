<?php
namespace GetResponse\Ecommerce;

use Translate;

/**
 * Class EcommerceValidator
 * @package GetResponse\Ecommerce
 */
class EcommerceValidator
{
    /** @var array */
    private $errors;

    /** @var Ecommerce */
    private $ecommerce;

    /**
     * @param Ecommerce $ecommerce
     */
    public function __construct(Ecommerce $ecommerce)
    {
        $this->ecommerce = $ecommerce;
        $this->errors = [];
        $this->validate();
    }

    private function validate()
    {
        if ($this->ecommerce->isEnabled() && empty($this->ecommerce->getShopId())) {
            $this->errors[] = Translate::getAdminTranslation('You need to select store');

            return;
        }

        if ($this->ecommerce->isEnabled() && empty($this->ecommerce->getListId())) {
            $this->errors[] = Translate::getAdminTranslation('You need to select list');

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
