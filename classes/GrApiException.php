<?php

class GrApiException extends Exception
{
    const CAMPAIGN_NOT_ADDED = 'Campaign has not been added';

    /**
     * @param Exception $e
     * @return GrApiException
     */
    public static function createForCampaignNotAddedException(Exception $e)
    {
        return new self(self::CAMPAIGN_NOT_ADDED . ' - ' . $e->getMessage(), $e->getCode());
    }

}
