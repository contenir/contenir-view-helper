<?php

declare(strict_types=1);

namespace Contenir\View\Helper\Factory;

use Contenir\View\Helper\Settings;
use Laminas\Config\Config;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Builds the settings() helper from the "settings" configuration.
 *
 * @api
 */
final class SettingsFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Settings
    {
        return new Settings(new Config(ConfigReader::section($container, 'settings')));
    }
}
