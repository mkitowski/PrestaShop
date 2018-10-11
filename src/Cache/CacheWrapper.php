<?php
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