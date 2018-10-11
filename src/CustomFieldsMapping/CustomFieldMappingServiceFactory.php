<?php
namespace GetResponse\CustomFieldsMapping;

use Db;
use GetResponse\Helper\Shop;
use GetResponseRepository;

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
        $repository = new GetResponseRepository(Db::getInstance(), Shop::getUserShopId());

        return new CustomFieldsMappingService($repository);

    }
}