<?php
namespace GetResponse\Account;

use Configuration;

/**
 * Class AccountRepository
 * @package GetResponse\Account
 */
class AccountRepository
{
    const INVALID_REQUEST = 'getresponse_invalid_request_date';
    const ORIGIN_CUSTOM_FIELD = 'getresponse_origin_custom_field';

    /**
     * @return string
     */
    public function getInvalidRequestDate()
    {
        return json_decode(Configuration::get(self::INVALID_REQUEST), true);
    }

    /**
     * @param string $date
     */
    public function updateInvalidRequestDate($date)
    {
        Configuration::updateValue(self::INVALID_REQUEST, $date);
    }

    public function clearInvalidRequestDate()
    {
        Configuration::updateValue(self::INVALID_REQUEST, NULL);
    }

    /**
     * @return string
     */
    public function getOriginCustomFieldValue()
    {
        return Configuration::get(self::ORIGIN_CUSTOM_FIELD);
    }

    /**
     * @param string $id
     */
    public function updateOriginCustomFieldId($id)
    {
        Configuration::updateValue(self::ORIGIN_CUSTOM_FIELD, $id);
    }

    public function clearOriginCustomFieldId()
    {
        Configuration::updateValue(self::ORIGIN_CUSTOM_FIELD, NULL);
    }
}
