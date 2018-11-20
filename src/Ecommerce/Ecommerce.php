<?php
namespace GetResponse\Ecommerce;

/**
 * Class Ecommerce
 * @package GetResponse\Ecommerce
 */
class Ecommerce
{
    /** @var string */
    private $shopId;

    /**
     * @param string $shopId
     */
    public function __construct($shopId)
    {
        $this->shopId = $shopId;
    }

    /**
     * @return string
     */
    public function getShopId()
    {
        return $this->shopId;
    }
}
