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
            return new Ecommerce(null);
        }

        return new Ecommerce($result['shop_id']);
    }

    /**
     * @param EcommerceDto $settings
     */
    public function updateEcommerceSubscription(EcommerceDto $settings)
    {
        Configuration::updateValue(
            ConfigurationSettings::ECOMMERCE,
            json_encode(['is_enabled' => $settings->isEnabled(), 'shop_id' => $settings->getShopId()])
        );
    }
}
