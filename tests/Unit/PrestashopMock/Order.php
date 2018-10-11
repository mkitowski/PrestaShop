<?php

/**
 * Class Order
 */
class Order
{
    /** @var int */
    public $id;

    /** @var float */
    public $total_paid_tax_excl;

    /** @var float */
    public $total_paid_tax_incl;

    /** @var float */
    public $id_currency;
    /** @var float */
    public $id_cart;

    /** @var float */
    public $total_shipping_tax_incl;

    /** @var string */
    public $date_add;

    /** @var string */
    public $id_customer;

    /** @var string */
    public $id_lang;

    /** @var int */
    public $id_address_delivery;

    /** @var int */
    public $id_address_invoice;

    /** @var array */
    private $products;

    /** @var string */
    private $current_state;

    /**
     * @param array $params
     */
    public function __construct($params)
    {
        $this->products = $params['products'];
        $this->id = isset($params['id']) ? $params['id'] : null;
        $this->total_paid_tax_excl = isset($params['total_paid_tax_excl']) ? $params['total_paid_tax_excl'] : null;
        $this->total_paid_tax_incl = isset($params['total_paid_tax_incl']) ? $params['total_paid_tax_incl'] : null;
        $this->id_currency = isset($params['id_currency']) ? $params['id_currency'] : null;
        $this->id_cart = isset($params['id_cart']) ? $params['id_cart'] : null;
        $this->total_shipping_tax_incl = isset($params['total_shipping_tax_incl']) ? $params['total_shipping_tax_incl'] : null;
        $this->date_add = isset($params['date_add']) ? $params['date_add'] : null;
        $this->id_customer = isset($params['id_customer']) ? $params['id_customer'] : null;
        $this->current_state = isset($params['current_state']) ? $params['current_state'] : null;
        $this->id_lang = isset($params['id_lang']) ? $params['id_lang'] : null;
        $this->id_address_delivery = isset($params['id_address_delivery']) ? $params['id_address_delivery'] : null;
        $this->id_address_invoice = isset($params['id_address_invoice']) ? $params['id_address_invoice'] : null;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return null|string
     */
    public function getCurrentState()
    {
        return $this->current_state;
    }
}