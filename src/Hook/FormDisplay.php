<?php
namespace GetResponse\Hook;

use GetResponse\WebForm\WebFormRepository;
use PrestaShopDatabaseException;

/**
 * Class FormDisplay
 * @package GetResponse\Hook
 */
class FormDisplay
{
    /** @var WebFormRepository */
    private $repository;

    public function __construct(WebFormRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $position
     * @return array
     */
    public function displayWebForm($position)
    {
        if (!empty($position)) {
            try {
                $webformSettings = $this->repository->getWebformSettings();

                if (!empty($webformSettings) && $webformSettings['active_subscription'] == 'yes'
                    && $webformSettings['sidebar'] == $position
                ) {
                    $setStyle = null;
                    if (!empty($webformSettings['style']) && $webformSettings['style'] == 'prestashop') {
                        $setStyle = '&css=1';
                    }

                    return array(
                        'webform_url' => $webformSettings['url'],
                        'style' => $setStyle,
                        'position' => $position
                    );
                }
            } catch (PrestaShopDatabaseException $e) {
                return array();
            }
        }

        return array();
    }
}