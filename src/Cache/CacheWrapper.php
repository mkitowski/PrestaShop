<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author     Getresponse <grintegrations@getresponse.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace GetResponse\Cache;

use Cache;
use CacheCore;
use GrShareCode\Cache\CacheInterface;

/**
 * Class Cache
 */
class CacheWrapper implements CacheInterface
{
    /** @var bool */
    private $isCacheEnabled;

    /** @var CacheCore $cache */
    private $cache;

    public function __construct()
    {
        $this->cache = Cache::getInstance();
        $this->isCacheEnabled = defined('_PS_CACHE_ENABLED_') ? _PS_CACHE_ENABLED_ : false;
    }

    /**
     * @param string $key
     * @param string $value
     * @param int $ttl
     */
    public function set($key, $value, $ttl)
    {
        if (!$this->isCacheEnabled) {
            return;
        }

        $this->cache->set($key, $value, $ttl);
    }

    /**
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        if (!$this->isCacheEnabled) {
            return null;
        }

        if (!$this->cache->exists($key)) {
            return null;
        }

        return $this->cache->get($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return $this->cache->exists($key);
    }
}
