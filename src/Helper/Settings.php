<?php

namespace Contenir\View\Helper;

use Laminas\Config\Config;
use Laminas\Config\Reader;
use Laminas\View\Helper\AbstractHelper;

class Settings extends AbstractHelper
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function __invoke()
    {
        return $this->config;
    }
}
