<?php

/**
 * Class CartProduct
 */
class CartProduct
{
    /** @var int */
    private $id_product;

    /** @var int */
    private $quantity;

    /**
     * @param int $id_product
     * @param int $quantity
     */
    public function __construct($id_product, $quantity)
    {
        $this->id_product = $id_product;
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getIdProduct()
    {
        return $this->id_product;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

}