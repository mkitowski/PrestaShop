<?php

namespace GetResponse\WebForm;

use Configuration;

/**
 * Class WebFormRepository
 */
class WebFormRepository
{
    const RESOURCE_KEY = 'getresponse_forms';

    /**
     * @param WebForm $webForm
     */
    public function update(WebForm $webForm)
    {
        Configuration::updateValue(
            self::RESOURCE_KEY,
            json_encode([
                'status' => $webForm->getStatus(),
                'webform_id' => $webForm->getId(),
                'sidebar' => $webForm->getSidebar(),
                'style' => $webForm->getStyle(),
                'url' => $webForm->getUrl()
            ])
        );
    }

    /**
     * @return WebForm|null
     */
    public function getWebForm()
    {
        $result = json_decode(Configuration::get(self::RESOURCE_KEY), true);

        if (empty($result)) {
            return WebForm::createEmptyInstance();
        }

        return new WebForm(
            $result['status'],
            $result['webform_id'],
            $result['sidebar'],
            $result['style'],
            $result['url']
        );
    }

    public function clearSettings()
    {
        Configuration::updateValue(self::RESOURCE_KEY, NULL);
    }
}
