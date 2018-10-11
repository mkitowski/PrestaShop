<?php
namespace GetResponse\Ecommerce;

/**
 * Class Ecommerce
 * @package GetResponse\Ecommerce
 */
class Ecommerce
{
    /** @var int */
    private $prestashopShopId;

    /** @var string */
    private $getResponseShopId;

    /**
     * @param int $prestashopShopId
     * @param string $getResponseShopId
     */
    public function __construct($prestashopShopId, $getResponseShopId)
    {
        $this->prestashopShopId = $prestashopShopId;
        $this->getResponseShopId = $getResponseShopId;
    }

    /**
     * @return int
     */
    public function getPrestashopShopId()
    {
        return $this->prestashopShopId;
    }

    /**
     * @return string
     */
    public function getGetResponseShopId()
    {
        return $this->getResponseShopId;
    }

    /**
     * @param array $result
     * @return Ecommerce
     */
    public static function fromDb(array $result)
    {
        return new self($result['id_shop'], $result['gr_id_shop']);
    }

}