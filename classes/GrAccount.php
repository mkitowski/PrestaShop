<?php

use GrShareCode\GetresponseApi;
use GrShareCode\GetresponseApiException;
/**
 * Class GrAccount
 *
 *  @author Getresponse <grintegrations@getresponse.com>
 *  @copyright GetResponse
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class GrAccount
{
    /** @var GetresponseApi $api */
    private $api;

    /** @var DbConnection $db */
    private $repository;

    /**
     * @param GetresponseApi $api
     * @param GetResponseRepository $repository
     */
    public function __construct(GetresponseApi $api, GetResponseRepository $repository)
    {
        $this->api = $api;
        $this->repository = $repository;
    }

    /**
     * @param string $apiKey
     * @param string $accountType
     * @param string $domain
     */
    public function updateApiSettings($apiKey, $accountType, $domain)
    {
        $this->repository->updateApiSettings($apiKey, $accountType, $domain);
    }

    public function checkConnection()
    {
        try {
            $this->api->checkConnection();
        } catch (GetresponseApiException $e) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     * @throws GetresponseApiException
     */
    public function getTrackingCode()
    {
        return $this->api->getTrackingCode();
    }

    /**
     * @return stdClass|false
     * @throws GetresponseApiException
     */
    public function getInfo()
    {
        return $this->api->getAccountInfo();
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->repository->getApiKey();
    }

    /**
     * @param string $activeTracking
     * @param string $snippet
     */
    public function updateTracking($activeTracking, $snippet)
    {
        $this->repository->updateTracking($activeTracking, $snippet);
    }

}
