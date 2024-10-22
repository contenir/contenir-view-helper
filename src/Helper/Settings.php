<?php

namespace Contenir\View\Helper;

use Laminas\Config\Config;
use Laminas\View\Helper\AbstractHelper;

class Settings extends AbstractHelper
{
    protected Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function __invoke(): Config
    {
        return $this->config;
    }
}
