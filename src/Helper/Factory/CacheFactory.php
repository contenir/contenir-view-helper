<?php

namespace Contenir\View\Helper\Factory;

use Contenir\View\Helper\Cache as CacheHelper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class CacheFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): CacheHelper
    {
        $cache = $container->get('ViewCache');

        return new CacheHelper($cache);
    }
}
