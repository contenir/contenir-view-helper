<?php

namespace Contenir\View\Helper\Factory;

use Application\Service\Manager\ResourceManager;
use Contenir\View\Helper\Resource as ResourceHelper;
use Psr\Container\ContainerInterface;

class ResourceFactory
{
    public function __invoke(ContainerInterface $container): ResourceHelper
    {
        $resourceManager = $container->get(ResourceManager::class);

        return new ResourceHelper($resourceManager);
    }
}
