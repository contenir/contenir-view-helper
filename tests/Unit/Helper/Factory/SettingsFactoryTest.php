<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit\Helper\Factory;

use Contenir\View\Helper\Factory\SettingsFactory;
use Contenir\View\Tests\TestAsset\Container\InMemoryContainer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(SettingsFactory::class)]
#[Group('unit')]
final class SettingsFactoryTest extends TestCase
{
    /**
     * @return array<string, array{array<string, mixed>, array<string, mixed>}>
     */
    public static function configProvider(): array
    {
        return [
            'settings section' => [['settings' => ['site_name' => 'Contenir']], ['site_name' => 'Contenir']],
            'no settings'      => [[], []],
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string, mixed> $expected
     */
    #[Test]
    #[DataProvider('configProvider')]
    public function exposesTheSettingsSection(array $config, array $expected): void
    {
        static::assertSame(
            $expected,
            (new SettingsFactory())(new InMemoryContainer(['config' => $config]))()->toArray(),
        );
    }
}
