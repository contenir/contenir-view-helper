<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit\Helper;

use Contenir\View\Helper\Settings;
use Laminas\Config\Config;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Settings::class)]
#[Group('unit')]
final class SettingsTest extends TestCase
{
    #[Test]
    public function returnsTheInjectedConfig(): void
    {
        $config = new Config(['site_name' => 'Contenir']);

        static::assertSame($config, (new Settings($config))());
    }
}
