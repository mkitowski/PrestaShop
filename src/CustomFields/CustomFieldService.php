<?php
namespace GetResponse\CustomFields;

use GrShareCode\CustomField\CustomFieldCollection;
use GrShareCode\CustomField\CustomFieldService as GrCustomFieldService;
use GrShareCode\Api\Exception\GetresponseApiException;

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
    public function getCustomFieldsFromGetResponse()
    {
        return $this->grCustomFieldService->getAllCustomFields();
    }
}
