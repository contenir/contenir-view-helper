<?php

namespace Contenir\View\Helper\Factory;

use Psr\Container\ContainerInterface;

class AssetSrcSetFactory
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName
    ) {
        $config = $container->get('config')['view_srcset'] ?? [];

        return new $requestedName($config);
    }
}
