<?php

declare(strict_types=1);

namespace Contenir\View\Helper\Factory;

use Contenir\View\Helper\Image as ImageHelper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Builds the image() helper from the "view_cdn" configuration.
 *
 * @api
 */
final class ImageFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): ImageHelper
    {
        return new ImageHelper(ConfigReader::section($container, 'view_cdn'));
    }
}
