<?php
namespace GetResponse\Account;

/**
 * Class AccountDto
 * @package GetResponse\Account
 */
class AccountDto
{
    const ENTERPRISE_PACKAGE_YES = '1';
    const ENTERPRISE_PACKAGE_NO = '0';

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
        $this->accountType = $accountType;
        $this->domain = $domain;
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
            $request['enterprisePackage'] === self::ENTERPRISE_PACKAGE_YES ? $request['accountType'] : AccountSettings::ACCOUNT_TYPE_SMB,
            $request['enterprisePackage'] === self::ENTERPRISE_PACKAGE_YES ? $request['domain'] : ''
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
        return $this->getEnterprisePackage() === self::ENTERPRISE_PACKAGE_YES;
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