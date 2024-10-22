<?php

namespace Contenir\View\Helper;

use Laminas\Cache\Exception\ExceptionInterface;
use Laminas\View\Helper\AbstractHelper;
use Laminas\Cache\Pattern\OutputCache;
use Laminas\Cache\Pattern\PatternOptions;
use Laminas\Cache\Storage\StorageInterface;

class Cache extends AbstractHelper
{
    protected OutputCache $cache;

    public function __construct(StorageInterface $storage = null)
    {
        $this->cache = new OutputCache(
            $storage,
            new PatternOptions()
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke($script = null, $key = null)
    {
        if ($script !== null && $key !== null) {
            $key     = $this->getSafeKey((string)$key);
            $storage = $this->cache->getStorage();
            if (! $storage->hasItem($key)) {
                $output = $this->getView()->render($script);
                $storage->setItem($key, $output);
            }

            return $storage->getItem($key);
        }

        return $this;
    }

    public function start($key): bool
    {
        $key = $this->getSafeKey($key);

        return $this->cache->start($key);
    }

    public function end(): bool
    {
        return $this->cache->end();
    }

    protected function getSafeKey(string $key): string
    {
        $key = preg_replace('/[^a-z0-9]+/', '_', strtolower($key));
        return preg_replace('/[_]{2,}/', '_', trim($key, '_'));
    }
}
