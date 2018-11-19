<?php

namespace GetResponse\WebForm;

use Configuration;
use ConfigurationSettings;

/**
 * Class WebFormRepository
 */
class WebFormRepository
{
    /**
     * @param WebForm $webForm
     */
    public function update(WebForm $webForm)
    {
        Configuration::updateValue(
            ConfigurationSettings::WEB_FORM,
            json_encode([
                'webform_id' => $webForm->getId(),
                'is_active' => $webForm->getStatus(),
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
        $result = json_decode(Configuration::get(ConfigurationSettings::WEB_FORM), true);

        if (empty($result)) {
            return WebForm::createEmptyInstance();
        }

        return new WebForm(
            $result['webform_id'],
            $result['is_active'],
            $result['sidebar'],
            $result['style'],
            $result['url']
        );
    }
}
