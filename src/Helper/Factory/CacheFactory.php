<?php

declare(strict_types=1);

namespace Contenir\View\Helper\Factory;

use Contenir\View\Helper\Cache as CacheHelper;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function get_debug_type;
use function sprintf;

/**
 * Builds the cache() helper on the application's "ViewCache" storage.
 *
 * @api
 */
final class CacheFactory
{
    public const string SERVICE = 'ViewCache';

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ServiceNotCreatedException when the service is not a cache storage.
     *
     * @mago-expect analysis:mixed-assignment Container services are untyped; the type is checked here.
     */
    public function __invoke(ContainerInterface $container): CacheHelper
    {
        $storage = $container->get(self::SERVICE);
        if (! $storage instanceof StorageInterface) {
            throw new ServiceNotCreatedException(sprintf(
                'Service "%s" must implement %s, %s given',
                self::SERVICE,
                StorageInterface::class,
                get_debug_type($storage),
            ));
        }

        return new CacheHelper($storage);
    }
}
