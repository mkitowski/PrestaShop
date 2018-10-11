<?php
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