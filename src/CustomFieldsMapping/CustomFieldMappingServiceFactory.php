<?php
namespace GetResponse\CustomFieldsMapping;

use Db;
use GetResponseRepository;
use GrShop;

/**
 * Class CustomFieldMappingServiceFactory
 * @package GetResponse\CustomFieldsMapping
 */
class CustomFieldMappingServiceFactory
{
    /**
     * @return CustomFieldsMappingService
     */
    public static function create()
    {
        $repository = new GetResponseRepository(Db::getInstance(), GrShop::getUserShopId());

        return new CustomFieldsMappingService($repository);

    }
}