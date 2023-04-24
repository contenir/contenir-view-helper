<?php

namespace Contenir\View\Helper\Factory;

use Application\Repository\ResourceCollectionRepository;
use Contenir\View\Helper\Collection as CollectionHelper;
use Psr\Container\ContainerInterface;

class CollectionFactory
{
    public function __invoke(ContainerInterface $container): CollectionHelper
    {
        $resourceCollectionRepository = $container->get(ResourceCollectionRepository::class);

        return new CollectionHelper($resourceCollectionRepository);
    }
}
