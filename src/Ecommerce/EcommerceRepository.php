<?php
namespace GetResponse\Ecommerce;

use Configuration;

/**
 * Class EcommerceRepository
 */
class EcommerceRepository
{
    const RESOURCE_KEY = 'getresponse_ecommerce';

    /**
     * @return Ecommerce
     */
    public function getEcommerceSettings()
    {
        $result = json_decode(Configuration::get(self::RESOURCE_KEY), true);

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
            self::RESOURCE_KEY,
            json_encode(['status' => $settings->getStatus(), 'shop_id' => $settings->getShopId(), 'list_id' => $settings->getListId()])
        );
    }

    public function clearEcommerceSettings()
    {
        Configuration::updateValue(self::RESOURCE_KEY, NULL);
    }
}
