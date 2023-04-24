<?php

namespace Contenir\View\Helper\Factory;

use Contenir\View\Helper\Cache as CacheHelper;
use Psr\Container\ContainerInterface;

class CacheFactory
{
    public function __invoke(ContainerInterface $container): CacheHelper
    {
        $cache = $container->get('ViewCache');

        return new CacheHelper($cache);
    }
}
