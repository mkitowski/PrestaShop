<?php
namespace GetResponse\CustomFields;

use GetResponse\CustomFieldsMapping\CustomFieldMapping;
use GetResponse\CustomFieldsMapping\CustomFieldMappingCollection;
use GrShareCode\CustomField\CustomFieldCollection;
use GrShareCode\CustomField\CustomFieldService as GrCustomFieldService;
use GrShareCode\GetresponseApiException;

/**
 * Class CustomFieldService
 */
class CustomFieldService
{
    /** @var GrCustomFieldService */
    private $grCustomFieldService;

    /**
     * @param GrCustomFieldService $grCustomFieldService
     */
    public function __construct(GrCustomFieldService $grCustomFieldService)
    {
        $this->grCustomFieldService = $grCustomFieldService;
    }

    /**
     * @return CustomFieldCollection
     * @throws GetresponseApiException
     */
    public function getAllCustomFields()
    {
        return $this->grCustomFieldService->getAllCustomFields();
    }

    /**
     * @param CustomFieldMappingCollection $customFieldMappingCollection
     * @throws GetresponseApiException
     */
    public function addCustomsIfMissing(CustomFieldMappingCollection $customFieldMappingCollection)
    {
        /** @var CustomFieldMapping $customFieldMapping */
        foreach ($customFieldMappingCollection as $customFieldMapping){

            if (!$this->grCustomFieldService->getCustomFieldByName($customFieldMapping->getName())) {

                $this->grCustomFieldService->createCustomField($customFieldMapping->getName(), null);
            }
        }
    }

}