<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author     Getresponse <grintegrations@getresponse.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace GetResponse\ContactList;

use Tools;
use Translate;

/**
 * Class AddContactListValidator
 * @package GetResponse\ContactList
 */
class AddContactListValidator
{
    /** @var array */
    private $errors;

    /** @var AddContactListDto */
    private $addContactListDto;

    /**
     * @param AddContactListDto $addContactListDto
     */
    public function __construct(AddContactListDto $addContactListDto)
    {
        $this->addContactListDto = $addContactListDto;
        $this->errors = [];
        $this->validate();
    }

    private function validate()
    {
        if (Tools::strlen($this->addContactListDto->getContactListName()) < 4) {
            $this->errors[] = Translate::getAdminTranslation('The "list name" field is invalid');
        }
        if ($this->addContactListDto->getFromField() === '') {
            $this->errors[] = Translate::getAdminTranslation('The "from" field is required');
        }
        if ($this->addContactListDto->getReplyTo() === '') {
            $this->errors[] = Translate::getAdminTranslation('The "reply-to" field is required');
        }
        if ($this->addContactListDto->getSubjectId() === '') {
            $this->errors[] = Translate::getAdminTranslation('The "confirmation subject" field is required');
        }
        if ($this->addContactListDto->getBodyId() === '') {
            $this->errors[] = Translate::getAdminTranslation('The "confirmation body" field is required');
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
