<?php
namespace GetResponse\Automation;

/**
 * Class AutomationFactory
 * @package GetResponse\Automation
 */
class AutomationFactory
{
    /**
     * @param array $result
     * @return Automation
     */
    public static function createFormDb(array $result)
    {
        return new Automation(
            $result['id'],
            $result['id_shop'],
            $result['category_id'],
            $result['campaign_id'],
            $result['action'],
            $result['cycle_day'],
            $result['active']
        );
    }
}