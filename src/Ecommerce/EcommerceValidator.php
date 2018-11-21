<?php
namespace GetResponse\Ecommerce;

use GetResponse\Settings\Registration\RegistrationRepository;
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

    /** @var RegistrationRepository */
    private $registrationRepository;

    /**
     * @param Ecommerce $ecommerce
     * @param RegistrationRepository $registrationRepository
     */
    public function __construct(Ecommerce $ecommerce, RegistrationRepository $registrationRepository)
    {
        $this->ecommerce = $ecommerce;
        $this->registrationRepository = $registrationRepository;
        $this->errors = [];
        $this->validate();
    }

    private function validate()
    {
        $registrationSettings = $this->registrationRepository->getSettings();

        if ($this->ecommerce->isEnabled() && empty($this->ecommerce->getShopId())) {
            $this->errors[] = Translate::getAdminTranslation('You need to select store');

            return;
        }

        if ($this->ecommerce->isEnabled() && !$registrationSettings->isActive()) {
            $this->errors[] = Translate::getAdminTranslation(
                'You need to enable adding contacts during registrations to enable ecommerce'
            );

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
