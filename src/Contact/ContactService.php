<?php
namespace GetResponse\Contact;

use GrShareCode\Contact\AddContactCommand;
use GrShareCode\Contact\ContactService as GrContactService;
use GrShareCode\GetresponseApiException;

/**
 * Class ContactService
 * @package GetResponse\Contact
 */
class ContactService
{
    /** @var GrContactService */
    private $grContactService;

    /**
     * @param GrContactService $grContactService
     */
    public function __construct(GrContactService $grContactService)
    {
        $this->grContactService = $grContactService;
    }

    /**
     * @param AddContactCommand $addContactCommand
     * @throws GetresponseApiException
     */
    public function addContact(AddContactCommand $addContactCommand)
    {
        $this->grContactService->upsertContact($addContactCommand);
    }
}