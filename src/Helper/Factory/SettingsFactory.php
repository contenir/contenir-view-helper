<?php

namespace Contenir\View\Helper\Factory;

use Contenir\View\Helper\Settings;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Laminas\Config\Config;
use Psr\Container\NotFoundExceptionInterface;

class SettingsFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Settings
    {
        $config = new Config($container->get('config')['settings'] ?? []);

        return new Settings($config);
    }
}
