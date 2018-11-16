<?php
namespace GetResponse\Account;

/**
 * Class AccountSettings
 * @package GetResponse\Account
 */
class AccountSettings
{
    const ACCOUNT_TYPE_SMB = 'smb';
    const ACCOUNT_TYPE_360_US = 'mx_us';
    const ACCOUNT_TYPE_360_PL = 'mx_pl';

    /** @var string */
    private $apiKey;

    /** @var string */
    private $accountType;

    /** @var string */
    private $domain;

    /**
     * @param string $apiKey
     * @param string $accountType
     * @param string $domain
     */
    public function __construct(
        $apiKey,
        $accountType,
        $domain
    ) {
        $this->apiKey = $apiKey;
        $this->accountType = $accountType;
        $this->domain = $domain;
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
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return bool
     */
    public function isConnectedWithGetResponse()
    {
        return !empty($this->getApiKey());
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return null|string
     */
    public function getHiddenApiKey()
    {
        if (strlen($this->getApiKey()) > 0) {
            return str_repeat('*', strlen($this->getApiKey()) - 6) . substr($this->getApiKey(), -6);
        }

        return null;
    }

    /**
     * @param array $params
     * @return AccountSettings
     */
    public static function createFromSettings($params)
    {
        return new self($params['api_key'], $params['type'], $params['domain']);
    }

    /**
     * @return AccountSettings
     */
    public static function createEmptyInstance()
    {
        return new self('', self::ACCOUNT_TYPE_SMB, '');
    }
}
