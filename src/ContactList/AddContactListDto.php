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
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace GetResponse\ContactList;

/**
 * Class AddContactListDto
 * @package GetResponse\ContactList
 */
class AddContactListDto
{
    /** @var string */
    private $contactListName;

    /** @var string */
    private $fromField;

    /** @var string */
    private $replyTo;

    /** @var string */
    private $subjectId;

    /** @var string */
    private $bodyId;

    /**
     * @param string $contactListName
     * @param string $fromField
     * @param string $replyTo
     * @param string $subjectId
     * @param string $bodyId
     */
    public function __construct($contactListName, $fromField, $replyTo, $subjectId, $bodyId)
    {
        $this->contactListName = $contactListName;
        $this->fromField = $fromField;
        $this->replyTo = $replyTo;
        $this->subjectId = $subjectId;
        $this->bodyId = $bodyId;
    }

    /**
     * @return string
     */
    public function getContactListName()
    {
        return $this->contactListName;
    }

    /**
     * @return string
     */
    public function getFromField()
    {
        return $this->fromField;
    }

    /**
     * @return string
     */
    public function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * @return string
     */
    public function getSubjectId()
    {
        return $this->subjectId;
    }

    /**
     * @return string
     */
    public function getBodyId()
    {
        return $this->bodyId;
    }
}
