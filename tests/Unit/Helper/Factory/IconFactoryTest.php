<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit\Helper\Factory;

use Contenir\View\Helper\Factory\ConfigReader;
use Contenir\View\Helper\Factory\IconFactory;
use Contenir\View\Tests\TestAsset\Container\InMemoryContainer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(IconFactory::class)]
#[CoversClass(ConfigReader::class)]
#[Group('unit')]
final class IconFactoryTest extends TestCase
{
    /**
     * @return array<string, array{mixed, string|null}>
     */
    public static function configProvider(): array
    {
        return [
            'configured class'      => [['view_helper_config' => ['icon' => ['class' => 'icon']]], 'icon'],
            'no helper config'      => [[], null],
            'no icon section'       => [['view_helper_config' => []], null],
            'non-string class'      => [['view_helper_config' => ['icon' => ['class' => 42]]], null],
            'non-array icon config' => [['view_helper_config' => ['icon' => 'icon']], null],
            'non-array config'      => ['config', null],
        ];
    }

    #[Test]
    #[DataProvider('configProvider')]
    public function appliesTheConfiguredWrapperClass(mixed $config, ?string $expected): void
    {
        static::assertSame($expected, (new IconFactory())(new InMemoryContainer(['config' => $config]))->getClass());
    }
}
