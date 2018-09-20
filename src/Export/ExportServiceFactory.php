<?php
namespace GetResponse\Export;

use Db;
use GrShop;

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
        $exportRepository = new ExportRepository(Db::getInstance(), GrShop::getUserShopId());

        return new ExportService($exportRepository);
    }
}