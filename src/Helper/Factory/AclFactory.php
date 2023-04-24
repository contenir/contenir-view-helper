<?php

namespace Contenir\View\Helper\Factory;

use Contenir\View\Helper\Acl as AclHelper;
use Application\Acl\Acl;
use Psr\Container\ContainerInterface;

class AclFactory
{
    public function __invoke(ContainerInterface $container): AclHelper
    {
        $acl = $container->get(Acl::class);

        return new AclHelper($acl);
    }
}
