<?php
namespace GetResponse\Ecommerce;

/**
 * Class EcommerceDto
 * @package GetResponse\Ecommerce
 */
class EcommerceDto
{
    const STATUS_ACTIVE = '1';
    const STATUS_INACTIVE = '0';

    /** @var string */
    private $shopId;

    /** @var string */
    private $ecommerceStatus;

    /**
     * @param string $shopId
     * @param string $ecommerceStatus
     */
    public function __construct($shopId, $ecommerceStatus)
    {
        $this->shopId = $shopId;
        $this->ecommerceStatus = $ecommerceStatus;
    }

    /**
     * @return string
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @return string
     */
    public function getEcommerceStatus()
    {
        return $this->ecommerceStatus;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->ecommerceStatus === self::STATUS_ACTIVE;
    }

}