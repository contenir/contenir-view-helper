<?php

namespace Contenir\View\Helper\Factory;

use Contenir\View\Helper\Icon as IconHelper;
use Psr\Container\ContainerInterface;

class IconFactory
{
    public function __invoke(ContainerInterface $container): IconHelper
    {
        $helper = new IconHelper();
        $config = $container->get('config');

        if (isset($config['view_helper_config']['icon'])) {
            $configHelper = $config['view_helper_config']['icon'];
            if (isset($configHelper['class'])) {
                $helper->setClass($configHelper['class']);
            }
        }

        return $helper;
    }
}
