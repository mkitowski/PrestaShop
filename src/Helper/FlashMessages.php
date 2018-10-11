<?php
namespace GetResponse\Helper;

use Context;
use Tools;

/**
 * Class FlashMessages
 */
class FlashMessages
{
    const TYPE_CONFIRMATION = 'confirmation';

    /**
     * @param string $type
     * @param string $message
     */
    public static function add($type, $message)
    {
        $cookie = Context::getContext()->cookie;
        $cookie_data = isset($cookie->getresponseflashmesseges)
            ? Tools::jsonDecode($cookie->getresponseflashmesseges, true)
            : [];

        if (!isset($cookie_data['flash_messages'])) {
            $cookie_data['flash_messages'] = [];
        }
        if (!isset($cookie_data['flash_messages'][$type])) {
            $cookie_data['flash_messages'][$type] = [];
        }
        $cookie_data['flash_messages'][$type][] = $message;
        $cookie->getresponseflashmesseges = Tools::jsonEncode($cookie_data);
    }

    /**
     * @param string $type
     * @return array
     */
    public static function getList($type)
    {
        $flash_messages = [];
        $cookie = Context::getContext()->cookie;
        $cookie_data = isset($cookie->getresponseflashmesseges)
            ? Tools::jsonDecode($cookie->getresponseflashmesseges, true)
            : [];

        if (isset($cookie_data['flash_messages'][$type])) {
            $flash_messages = $cookie_data['flash_messages'][$type];
            unset($cookie_data['flash_messages'][$type]);
            $cookie->getresponseflashmesseges = Tools::jsonEncode($cookie_data);
        }

        return $flash_messages;
    }

    /**
     * @return array
     */
    public static function getConfirmations()
    {
        return self::getList(self::TYPE_CONFIRMATION);
    }

}