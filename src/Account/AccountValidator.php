<?php
namespace GetResponse\Account;

use Translate;

/**
 * Class AccountValidator
 * @package GetResponse\Account
 */
class AccountValidator
{
    /** @var array */
    private $errors;

    /** @var AccountDto */
    private $accountDto;

    /**
     * @param AccountDto $accountDto
     */
    public function __construct(AccountDto $accountDto)
    {
        $this->accountDto = $accountDto;
        $this->errors = [];
        $this->validate();
    }

    private function validate()
    {
        if (empty($this->accountDto->getApiKey())) {
            $this->errors[] = Translate::getAdminTranslation('You need to enter API key. This field can\'t be empty.');

            return;
        }

        if ($this->accountDto->isEnterprisePackage() && empty($this->accountDto->getAccountType())) {
            $this->errors[] = Translate::getAdminTranslation('Invalid account type.');

            return;
        }

        if ($this->accountDto->isEnterprisePackage() && empty($this->accountDto->getDomain())) {
            $this->errors[] = Translate::getAdminTranslation('Domain field can not be empty.');

            return;
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}