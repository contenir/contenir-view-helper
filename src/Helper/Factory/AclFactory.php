<?php

namespace Contenir\View\Helper\Factory;

use Contenir\View\Helper\Acl as AclHelper;
use Application\Acl\Acl;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class AclFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): AclHelper
    {
        $acl = $container->get(Acl::class);

        return new AclHelper($acl);
    }
}
