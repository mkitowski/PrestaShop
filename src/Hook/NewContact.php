<?php
namespace GetResponse\Hook;

use GetResponse\Contact\ContactDto;
use GrShareCode\GetresponseApi;
use GetResponse\Account\AccountServiceFactory as GrAccountServiceFactory;
use GetResponseRepository;
use Db;
use GrShareCode\Contact\AddContactCommand as GrAddContactCommand;
use GrShareCode\Contact\ContactService as GrContactService;
use GrShareCode\Contact\CustomFieldsCollection as GrCustomFieldsCollection;
use GrShareCode\Contact\CustomField as GrCustomField;
use PrestaShopDatabaseException;
use GrShareCode\GetresponseApiException;


/**
 * Class NewContact
 * @package GetResponse\Hook
 */
class NewContact extends Hook
{
    /** @var GetresponseApi */
    private $api;

    /** @var Db */
    private $db;

    /** @var GetResponseRepository */
    private $repository;

    /**
     * @param GetresponseApi $api
     * @param GetResponseRepository $repository
     * @param Db $db
     */
    public function __construct(GetresponseApi $api, GetResponseRepository $repository, Db $db)
    {
        $this->api = $api;
        $this->db = $db;
        $this->repository = $repository;
    }

    /**
     * @param ContactDto $contactDto
     * @throws PrestaShopDatabaseException
     * @throws GetresponseApiException
     */
    public function sendContact(ContactDto $contactDto)
    {
        $accountService = GrAccountServiceFactory::create();
        $settings = $accountService->getSettings();

        if ($settings->getActiveSubscription() == 'yes' && !empty($settings->getCampaignId())) {
            if (true === $contactDto->getNewsletter()) {
                $addContact = new GrAddContactCommand(
                    $contactDto->getEmail(),
                    $contactDto->getName(),
                    $settings->getCampaignId(),
                    $settings->getCycleDay(),
                    $this->mapCustomFields(
                        $contactDto->getCustomFields(),
                        $settings->getUpdateAddress() == 'yes'
                    ),
                    $contactDto->getOrigin()
                );

                $contactService = new GrContactService($this->api);
                $contactService->upsertContact($addContact);

            }
        }
    }


    /**
     * @param array $contact
     * @param bool $useCustoms
     * @return GrCustomFieldsCollection
     * @throws PrestaShopDatabaseException
     */
    private function mapCustomFields($contact, $useCustoms)
    {
        $c = array();

        /** @var GrCustomField $grCustom */
        foreach ($this->grCustoms as $grCustom) {
            $c[$grCustom->getName()] = $grCustom->getId();
        }

        $collection = new GrCustomFieldsCollection();

        if (false === $useCustoms) {
            return $collection;
        }

        $mappingCollection = $this->repository->getCustoms();

        foreach ($mappingCollection as $mapping) {
            if (!isset($c[$mapping['custom_name']])) {
                continue;
            }
            if ('yes' === $mapping['active_custom'] && isset($contact[$mapping['custom_name']])) {
                $collection->add(new GrCustomField($c[$mapping['custom_name']], $mapping['custom_name'], $contact[$mapping['custom_name']]));
            }
        }

        return $collection;
    }


}