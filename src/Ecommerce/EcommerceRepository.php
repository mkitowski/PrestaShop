<?php
namespace GetResponse\Ecommerce;

use Configuration;
use ConfigurationSettings;

/**
 * Class EcommerceRepository
 */
class EcommerceRepository
{
    /**
     * @return Ecommerce
     */
    public function getEcommerceSettings()
    {
        $result = json_decode(Configuration::get(ConfigurationSettings::ECOMMERCE), true);

        if (empty($result)) {
            return new Ecommerce(Ecommerce::STATUS_INACTIVE, null);
        }

        return new Ecommerce($result['status'], $result['shop_id']);
    }

    /**
     * @param Ecommerce $settings
     */
    public function updateEcommerceSubscription(Ecommerce $settings)
    {
        Configuration::updateValue(
            ConfigurationSettings::ECOMMERCE,
            json_encode(['status' => $settings->getStatus(), 'shop_id' => $settings->getShopId()])
        );
    }
}
