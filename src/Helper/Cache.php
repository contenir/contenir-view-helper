<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\Cache\Exception\ExceptionInterface;
use Laminas\Cache\Pattern\OutputCache;
use Laminas\Cache\Pattern\PatternOptions;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\AbstractHelper;

use function is_string;
use function preg_replace;
use function strtolower;
use function trim;

/**
 * Caches rendered view output in a laminas-cache storage: either a whole
 * partial (`$this->cache('partial/nav', 'nav')`) or an inline block between
 * start() and end().
 *
 * @api
 */
final class Cache extends AbstractHelper
{
    private OutputCache $cache;

    public function __construct(StorageInterface $storage)
    {
        $this->cache = new OutputCache($storage, new PatternOptions());
    }

    private static function stringOrEmpty(mixed $value): string
    {
        return is_string($value) ? $value : '';
    }

    /**
     * Stores and echoes the block captured since start().
     *
     * @throws ExceptionInterface when no block was started.
     */
    public function end(): bool
    {
        return $this->cache->end();
    }

    /**
     * Echoes the cached block and returns true on a hit; otherwise starts
     * capturing output until end() and returns false.
     *
     * @throws ExceptionInterface on a storage failure or an empty key.
     */
    public function start(int|string $key): bool
    {
        return $this->cache->start($this->getSafeKey((string) $key));
    }

    private function getSafeKey(string $key): string
    {
        $key = (string) preg_replace('/[^a-z0-9]+/', replacement: '_', subject: strtolower($key));

        return (string) preg_replace('/_{2,}/', replacement: '_', subject: trim($key, characters: '_'));
    }

    /**
     * @throws RuntimeException when the helper has no renderer.
     */
    private function render(string $script): string
    {
        $view = $this->getView();
        if (null === $view) {
            throw new RuntimeException('The cache helper needs a renderer to render a script');
        }

        return $view->render($script);
    }

    /**
     * With a script and a key, returns that script's cached output, rendering
     * and storing it first on a miss. Without both, returns the helper.
     *
     * @throws ExceptionInterface on a storage failure.
     * @throws RuntimeException when rendering without a renderer.
     */
    public function __invoke(?string $script = null, int|string|null $key = null): self|string
    {
        if (null === $script || null === $key) {
            return $this;
        }

        $key     = $this->getSafeKey((string) $key);
        $storage = $this->cache->getStorage();

        if (! $storage->hasItem($key)) {
            $storage->setItem($key, $this->render($script));
        }

        return self::stringOrEmpty($storage->getItem($key));
    }
}
