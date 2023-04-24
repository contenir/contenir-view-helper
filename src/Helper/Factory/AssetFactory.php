<?php

namespace Contenir\View\Helper\Factory;

use Application\Service\Manager\AssetManager;
use Contenir\View\Helper\Asset as AssetHelper;
use Psr\Container\ContainerInterface;

class AssetFactory
{
    public function __invoke(ContainerInterface $container): AssetHelper
    {
		$config = $container->get('config')['view_asset'] ?? [];
		$assetManagerClass = $config['asset_manager'] ?? null;

        $assetManager = $container->get($assetManagerClass);

        return new AssetHelper($assetManager);
    }
}
