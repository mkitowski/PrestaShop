<?php
namespace GetResponse\Ecommerce;

/**
 * Class Ecommerce
 * @package GetResponse\Ecommerce
 */
class Ecommerce
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /** @var string */
    private $shopId;

    /** @var string */
    private $status;

    /**
     * @param string $status
     * @param string $shopId
     */
    public function __construct($status, $shopId)
    {
        $this->status = $status;
        $this->shopId = $shopId;
    }

    public static function createFromPost($params)
    {
        if ($params['ecommerce']) {
            return new self(
                self::STATUS_ACTIVE,
                $params['shop']
            );
        } else {
            return new self(
                self::STATUS_INACTIVE,
                null
            );
        }

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
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
