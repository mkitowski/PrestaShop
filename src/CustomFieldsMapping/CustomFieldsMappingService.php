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
     * @param CustomFieldMapping $newCustomFieldMapping
     * @throws CustomFieldMappingException
     */
    public function updateCustomFieldMapping(CustomFieldMapping $newCustomFieldMapping)
    {
        $customFieldMapping = $this->getCustomFieldMappingById($newCustomFieldMapping->getId());

        if (!$customFieldMapping) {
            throw CustomFieldMappingException::createForNotFoundCustomFieldMapping($newCustomFieldMapping->getId());
        }

        if ($customFieldMapping->isDefault()) {
            throw CustomFieldMappingException::createForDefaultCustomFieldMapping($newCustomFieldMapping->getId());
        }

        $this->repository->updateCustom($newCustomFieldMapping);
    }

    /**
     * @return CustomFieldMappingCollection
     */
    public function getActiveCustomFieldMapping()
    {
        $customFieldMappingCollection = new CustomFieldMappingCollection();

        foreach ($this->repository->getCustomFieldsMapping() as $customFieldMapping) {
            if (!$customFieldMapping->isDefault() && $customFieldMapping->isActive()){
                $customFieldMappingCollection->add($customFieldMapping);
            }
        }

        return $customFieldMappingCollection;
    }

    /**
     * @param int $customFieldMappingId
     * @return CustomFieldMapping|null
     */
    public function getCustomFieldMappingById($customFieldMappingId)
    {
        foreach ($this->repository->getCustomFieldsMapping() as $customFieldMapping) {
            if ($customFieldMappingId == $customFieldMapping->getId()) {
                return $customFieldMapping;
            }
        }

        return null;
    }
}
