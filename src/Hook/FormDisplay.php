<?php
namespace GetResponse\Hook;

use GetResponse\WebForm\WebFormService;

/**
 * Class FormDisplay
 * @package GetResponse\Hook
 */
class FormDisplay
{
    /** @var WebFormService */
    private $webFormService;

    /**
     * @param WebFormService $webFormService
     */
    public function __construct(WebFormService $webFormService)
    {
        $this->webFormService = $webFormService;
    }

    /**
     * @param string $position
     * @return array
     */
    public function displayWebForm($position)
    {
        if (empty($position)) {
            return [];
        }

        $webForm = $this->webFormService->getWebForm();

        if (!$webForm
            || !$webForm->isStatusActive()
            || !$webForm->hasSamePosition($position)) {

            return [];
        }

        $setStyle = $webForm->hasPrestashopStyle() ? '&css=1' : null;

        return [
            'webform_url' => $webForm->getUrl(),
            'style' => $setStyle,
            'position' => $position
        ];
    }
}
