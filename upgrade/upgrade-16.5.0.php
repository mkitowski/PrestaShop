<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_16_5_0($object)
{
    update_customs();
    return true;
}


function update_customs()
{
    $raw_customs = Configuration::get('getresponse_customs');

    if (empty($raw_customs)) {
        Configuration::updateValue(
            'getresponse_customs',
            json_encode([])
        );
        return;
    }

    $old_customs = json_decode($raw_customs, true);
    $new_customs = [];

    foreach ($old_customs as $custom) {
        if (1 == $custom['is_active'] && 1 != $custom['is_default']) {
            $new_customs[] = [
                'customer_property_name' => $custom['customer_property_name'],
                'gr_custom_id' => $custom['gr_custom_id'],
            ];
        }
    }

    Configuration::updateValue(
        'getresponse_customs',
        json_encode($new_customs)
    );
}