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
    private $status;

    /** @var string */
    private $shopId;

    /** @var string */
    private $listId;

    /**
     * @param string $status
     * @param string $shopId
     * @param string $listId
     */
    public function __construct($status, $shopId, $listId)
    {
        $this->status = $status;
        $this->shopId = $shopId;
        $this->listId = $listId;
    }

    /**
     * @param array $params
     * @return Ecommerce
     */
    public static function createFromPost(array $params)
    {
        if ($params['ecommerce']) {
            return new self(
                self::STATUS_ACTIVE,
                $params['shop'],
                $params['list']
            );
        } else {
            return new self(
                self::STATUS_INACTIVE,
                null,
                null
            );
        }
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
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
    public function getListId()
    {
        return $this->listId;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
