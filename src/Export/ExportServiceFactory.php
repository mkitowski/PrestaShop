<?php
namespace GetResponse\Export;

use Db;
use GetResponse\Helper\Shop;

/**
 * Class ExportServiceFactory
 * @package GetResponse\Export
 */
class ExportServiceFactory
{

    /**
     * @return ExportService
     */
    public static function create()
    {
        $exportRepository = new ExportRepository(Db::getInstance(), Shop::getUserShopId());

        return new ExportService($exportRepository);
    }
}