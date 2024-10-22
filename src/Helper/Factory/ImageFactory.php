<?php

namespace Contenir\View\Helper\Factory;

use Contenir\View\Helper\Image as ImageHelper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ImageFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): ImageHelper
    {
        $config = $container->get('config')['view_cdn'] ?? [];

        return new ImageHelper($config);
    }
}
