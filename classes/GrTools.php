<?php

use GrShareCode\Api\ApiType as GrApiType;
use GrShareCode\GetresponseApi;

class GrTools
{
    const X_APP_ID = '2cd8a6dc-003f-4bc3-ba55-c2e4be6f7500';

    /**
     * @param array $settings
     * @return \GrShareCode\GetresponseApi
     * @throws \GrShareCode\Api\ApiTypeException
     */
    public static function getApiInstance(array $settings)
    {
        switch ($settings['account_type']) {
            case "360en":
                $type = GrApiType::createForMxUs($settings['crypto']);
                break;
            case "360pl":
                $type = GrApiType::createForMxPl($settings['crypto']);
                break;
            default:
                $type = GrApiType::createForSMB();
                break;
        }

        return new GetresponseApi($settings['api_key'], $type, self::X_APP_ID);
    }
}
