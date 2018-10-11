<?php

/**
 * Class Country
 */
class Country
{
    /** @var string  */
    public $iso_code;

    /** @var string  */
    public $name;

    /**
     * @param int $countryId
     */
    public function __construct($countryId)
    {
        $this->iso_code = 'PL';
        $this->name = 'Poland';
    }


}