<?php

/**
 * Class Cart
 */
class Cart
{
    /** @var int */
    public $id;

    /** @var int */
    public $id_currency;

    /** @var int */
    public $id_customer;

    /** @var array */
    private $products;

    /** @var float */
    private $total;

    /** @var float */
    private $total_with_tax;

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->products = $params['products'];
        $this->id = isset($params['id']) ? $params['id'] : null;
        $this->total = isset($params['total']) ? $params['total'] : null;
        $this->total_with_tax = isset($params['total_with_tax']) ? $params['total_with_tax'] : null;
        $this->id_currency = isset($params['id_currency']) ? $params['id_currency'] : null;
        $this->id_customer = isset($params['id_customer']) ? $params['id_customer'] : null;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param bool $taxIncluded
     * @return float
     */
    public function getOrderTotal($taxIncluded)
    {
        return $taxIncluded ? $this->total : $this->total_with_tax;
    }
}