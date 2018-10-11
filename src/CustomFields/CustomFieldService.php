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
     * @param CustomFieldMappingCollection $customFieldMappingCollection
     * @return CustomFieldCollection
     * @throws GetresponseApiException
     */
    public function getCustomFieldsFromGetResponse(CustomFieldMappingCollection $customFieldMappingCollection)
    {

//        @todo: When new method available in shareCode
//        $customFields = [];
//
//        /** @var CustomFieldMapping $customFieldMapping */
//        foreach ($customFieldMappingCollection as $customFieldMapping){
//            $customFields[] = $customFieldMapping->getName();
//        }
//
//        $customFieldList = implode(',', $customFields);
//        $this->grCustomFieldService->getAllCustomFieldsWithNames($customFieldList);

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

                $this->grCustomFieldService->createCustomField($customFieldMapping->getName(), $customFieldMapping->getName());
            }
        }
    }

}