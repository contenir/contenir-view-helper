<?php

namespace Contenir\View\Helper\Factory;

use Contenir\View\Helper\AssetUrl;
use Psr\Container\ContainerInterface;

class AssetUrlFactory
{
    public function __invoke(ContainerInterface $container): AssetUrl
    {
        $config = $container->get('config')['view_cdn'] ?? [];

        return new AssetUrl($config);
    }
}
