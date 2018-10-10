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

    /** @var EcommerceDto */
    private $ecommerceDto;

    /** @var EcommerceService */
    private $ecommerceService;

    /**
     * @param EcommerceDto $ecommerceDto
     * @param EcommerceService $ecommerceService
     */
    public function __construct(EcommerceDto $ecommerceDto, EcommerceService $ecommerceService)
    {
        $this->ecommerceDto = $ecommerceDto;
        $this->ecommerceService = $ecommerceService;
        $this->errors = [];
        $this->validate();
    }

    private function validate()
    {
        if ($this->ecommerceDto->isEnabled() && empty($this->ecommerceDto->getShopId())) {
            $this->errors[] = Translate::getAdminTranslation('You need to select store');

            return;
        }

        if ($this->ecommerceDto->isEnabled() && !$this->ecommerceService->isSubscribeViaRegistrationActive()) {
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