<?php
namespace GetResponse\CustomFieldsMapping;

use GetResponseRepository;

/**
 * Class CustomFieldsMappingService
 * @package GetResponse\CustomFieldsMapping
 */
class CustomFieldsMappingService
{

    /** @var GetResponseRepository */
    private $repository;

    /**
     * @param GetResponseRepository $repository
     */
    public function __construct(GetResponseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CustomFieldMapping $customFieldMapping
     * @throws CustomFieldMappingException
     */
    public function updateCustomFieldMapping(CustomFieldMapping $customFieldMappingFromRequest)
    {
        $customFieldMapping = $this->getCustomFieldMappingById($customFieldMappingFromRequest->getId());

        if (!$customFieldMapping) {
            throw CustomFieldMappingException::createForNotFoundCustomFieldMapping($customFieldMapping->getId());
        }

        if ($customFieldMapping->isDefault()) {
            throw CustomFieldMappingException::createForDefaultCustomFieldMapping($customFieldMapping->getId());
        }

        $this->repository->updateCustom($customFieldMappingFromRequest);
    }

    /**
     * @return CustomFieldMappingCollection
     */
    public function getAllCustomFieldMapping()
    {
        $customFieldMappingCollection = new CustomFieldMappingCollection();

        foreach ($this->repository->getCustoms() as $customFields) {
            $customFieldMappingCollection->add(
                new CustomFieldMapping(
                    $customFields['id'],
                    $customFields['value'],
                    $customFields['name'],
                    $customFields['active']
                )
            );
        }

        return $customFieldMappingCollection;
    }

    /**
     * @param int $customFieldMappingId
     * @return CustomFieldMapping|null
     */
    public function getCustomFieldMappingById($customFieldMappingId)
    {
        foreach ($this->repository->getCustoms() as $customFields) {

            if ($customFieldMappingId === $customFields['id_custom']) {

                return new CustomFieldMapping(
                    $customFields['id_custom'],
                    $customFields['custom_value'],
                    $customFields['custom_name'],
                    $customFields['active_custom'],
                    $customFields['custom_field'],
                    $customFields['default']
                );
            }
        }

        return null;
    }
}