<?php

namespace Contenir\View\Helper\Factory;

use Contenir\View\Helper\Image as ImageHelper;
use Application\Service\Acl;
use Psr\Container\ContainerInterface;

class ImageFactory
{
    public function __invoke(ContainerInterface $container): ImageHelper
    {
        $config = $container->get('config')['view_cdn'] ?? [];

        return new ImageHelper($config);
    }
}
