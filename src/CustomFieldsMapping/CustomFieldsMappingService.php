<?php
namespace GetResponse\CustomFieldsMapping;

use GetResponseRepository;
use PrestaShopDatabaseException;

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
     * @param CustomFieldMapping $customFieldMappingFromRequest
     * @throws CustomFieldMappingException
     * @throws PrestaShopDatabaseException
     */
    public function updateCustomFieldMapping(CustomFieldMapping $customFieldMappingFromRequest)
    {
        $customFieldMapping = $this->getCustomFieldMappingById($customFieldMappingFromRequest->getId());

        if (!$customFieldMapping) {
            throw CustomFieldMappingException::createForNotFoundCustomFieldMapping($customFieldMappingFromRequest->getId());
        }

        if ($customFieldMapping->isDefault()) {
            throw CustomFieldMappingException::createForDefaultCustomFieldMapping($customFieldMappingFromRequest->getId());
        }

        $this->repository->updateCustom($customFieldMappingFromRequest);
    }

    /**
     * @return CustomFieldMappingCollection
     * @throws PrestaShopDatabaseException
     */
    public function getActiveCustomFieldMapping()
    {
        $customFieldMappingCollection = new CustomFieldMappingCollection();

        foreach ($this->repository->getCustoms() as $customFields) {

            $customFieldMapping = new CustomFieldMapping(
                $customFields['id_custom'],
                $customFields['custom_value'],
                $customFields['custom_name'],
                $customFields['active_custom'],
                $customFields['custom_field'],
                $customFields['default']
            );

            if (!$customFieldMapping->isDefault() && $customFieldMapping->isActive()){
                $customFieldMappingCollection->add($customFieldMapping);
            }
        }

        return $customFieldMappingCollection;
    }

    /**
     * @param int $customFieldMappingId
     * @return CustomFieldMapping|null
     * @throws PrestaShopDatabaseException
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