<?php

/**
 * Class Currency
 */
class Currency
{
    /** @var string */
    public $iso_code;

    /**
     * @param int $currencyId
     */
    public function __construct($currencyId)
    {
        $this->iso_code = 'PLN';
    }
}