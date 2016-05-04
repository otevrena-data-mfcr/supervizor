<?php

namespace Supervizor\Storage;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;

class CacheStorage
{
    /** @var IStorage */
    private $storage;

    /** @var Cache */
    private $cache;

    const EXPIRATION = '5 hours';



    /**
     * CacheStorage constructor.
     * @param IStorage $storage
     */
    public function __construct(IStorage $storage)
    {
        $this->storage = $storage;
        $this->cache = new Cache($this->storage, 'Ajax');
    }



    public function load($key, $fallback)
    {
        return $this->cache->load($key, $fallback);
    }

}
