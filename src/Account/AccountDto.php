<?php
namespace GetResponse\Account;

/**
 * Class AccountDto
 * @package GetResponse\Account
 */
class AccountDto
{
    /** @var string */
    private $apiKey;

    /** @var string */
    private $enterprisePackage;

    /** @var string */
    private $accountType;

    /** @var string */
    private $domain;

    /**
     * @param string $apiKey
     * @param string $enterprisePackage
     * @param string $accountType
     * @param string $domain
     */
    public function __construct($apiKey, $enterprisePackage, $accountType, $domain)
    {
        $this->apiKey = $apiKey;
        $this->enterprisePackage = $enterprisePackage;
        $this->accountType = $enterprisePackage === '1' ? $accountType : AccountSettings::ACCOUNT_TYPE_SMB;
        $this->domain = $enterprisePackage === '1' ? $domain : '';
    }

    /**
     * @param array $request
     * @return AccountDto
     */
    public static function fromRequest(array $request)
    {
        return new self(
            $request['apiKey'],
            $request['enterprisePackage'],
            $request['accountType'],
            $request['domain']
        );
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * @return string
     */
    public function getAccountTypeForSettings()
    {
        return $this->isEnterprisePackage() ? $this->accountType : AccountSettings::ACCOUNT_TYPE_SMB;
    }

    /**
     * @return bool
     */
    public function isEnterprisePackage()
    {
        return $this->getEnterprisePackage() === '1';
    }

    /**
     * @return string
     */
    public function getEnterprisePackage()
    {
        return $this->enterprisePackage;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

}