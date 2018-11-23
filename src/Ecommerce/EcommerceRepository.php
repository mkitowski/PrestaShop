<?php
namespace GetResponse\Ecommerce;

use Configuration;
use GetResponse\Config\ConfigurationKeys;

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
        $result = json_decode(Configuration::get(ConfigurationKeys::ECOMMERCE), true);

        if (empty($result)) {
            return new Ecommerce(Ecommerce::STATUS_INACTIVE, null, null);
        }

        return new Ecommerce($result['status'], $result['shop_id'], $result['list_id']);
    }

    /**
     * @param Ecommerce $settings
     */
    public function updateEcommerceSubscription(Ecommerce $settings)
    {
        Configuration::updateValue(
            ConfigurationKeys::ECOMMERCE,
            json_encode(['status' => $settings->getStatus(), 'shop_id' => $settings->getShopId(), 'list_id' => $settings->getListId()])
        );
    }
}
