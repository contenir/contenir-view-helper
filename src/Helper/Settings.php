<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\Config\Config;
use Laminas\View\Helper\AbstractHelper;

/**
 * Exposes the application's "settings" configuration to view scripts.
 *
 * @api
 */
final class Settings extends AbstractHelper
{
    public function __construct(
        private Config $config,
    ) {}

    public function __invoke(): Config
    {
        return $this->config;
    }
}
