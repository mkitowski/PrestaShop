<?php
namespace GetResponse\WebForm;

/**
 * Class WebFormFactory
 */
class WebFormFactory
{
    /**
     * @param array $dbResults
     * @return WebForm
     */
    public static function fromDb(array $dbResults)
    {
        return new WebForm(
            $dbResults['webform_id'],
            $dbResults['active_subscription'],
            $dbResults['sidebar'],
            $dbResults['style'],
            $dbResults['url']
        );
    }

    /**
     * @param array $request
     * @return WebForm
     */
    public static function fromRequest(array $request)
    {
        return new WebForm(
            $request['formId'],
            empty($request['status']) ? WebForm::STATUS_INACTIVE : $request['status'],
            empty($request['sidebar']) ? WebForm::SIDEBAR_DEFAULT : $request['sidebar'],
            empty($request['style']) ? WebForm::STYLE_DEFAULT : $request['style'],
            $request['url']
        );
    }
}