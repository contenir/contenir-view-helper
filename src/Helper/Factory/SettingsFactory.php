<?php

namespace Contenir\View\Helper\Factory;

use Contenir\View\Helper\Settings;
use Psr\Container\ContainerInterface;
use Laminas\Config\Config;

class SettingsFactory
{
    public function __invoke(ContainerInterface $container): Settings
    {
        $config = new Config($container->get('config')['settings'] ?? []);

        return new Settings($config);
    }
}
