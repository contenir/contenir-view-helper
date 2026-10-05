<?php

declare(strict_types=1);

namespace Contenir\View\Helper\Factory;

use Contenir\View\Helper\Icon as IconHelper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function is_string;

/**
 * Builds the icon() helper, applying a default wrapper class from
 * "view_helper_config.icon.class".
 *
 * @api
 */
final class IconFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @mago-expect analysis:mixed-assignment Configuration is untyped input; narrowed here.
     */
    public function __invoke(ContainerInterface $container): IconHelper
    {
        $helper = new IconHelper();
        $class  = ConfigReader::section($container, 'view_helper_config', 'icon')['class'] ?? null;

        if (is_string($class)) {
            $helper->setClass($class);
        }

        return $helper;
    }
}
