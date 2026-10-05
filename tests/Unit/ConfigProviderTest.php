<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit;

use Contenir\View\ConfigProvider;
use Contenir\View\Helper;
use Laminas\ServiceManager\Factory\InvokableFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function array_keys;
use function array_unique;
use function array_values;
use function lcfirst;
use function sort;
use function ucfirst;

#[CoversClass(ConfigProvider::class)]
#[Group('unit')]
final class ConfigProviderTest extends TestCase
{
    /**
     * @return array<string, array{string, class-string}>
     */
    public static function aliasProvider(): array
    {
        $cases = [];
        foreach ((new ConfigProvider())->getViewHelperConfig()['aliases'] as $alias => $helper) {
            $cases[$alias] = [$alias, $helper];
        }

        return $cases;
    }

    /**
     * @return array<string, array{class-string, class-string}>
     */
    public static function factoryProvider(): array
    {
        return [
            'acl'      => [Helper\Acl::class, Helper\Factory\AclFactory::class],
            'cache'    => [Helper\Cache::class, Helper\Factory\CacheFactory::class],
            'icon'     => [Helper\Icon::class, Helper\Factory\IconFactory::class],
            'image'    => [Helper\Image::class, Helper\Factory\ImageFactory::class],
            'settings' => [Helper\Settings::class, Helper\Factory\SettingsFactory::class],
            'truncate' => [Helper\Truncate::class, InvokableFactory::class],
        ];
    }

    #[Test]
    #[DataProvider('aliasProvider')]
    public function aliasesEachHelperInBothCamelAndPascalCase(string $alias, string $helper): void
    {
        $aliases = (new ConfigProvider())->getViewHelperConfig()['aliases'];

        static::assertSame(
            [$helper, $helper],
            [$aliases[lcfirst($alias)] ?? null, $aliases[ucfirst($alias)] ?? null],
        );
    }

    #[Test]
    public function aliasesTargetRegisteredHelpers(): void
    {
        $config  = (new ConfigProvider())->getViewHelperConfig();
        $helpers = array_keys($config['factories']);
        $aliased = array_values(array_unique($config['aliases']));
        sort($helpers);
        sort($aliased);

        static::assertSame($helpers, $aliased);
    }

    #[Test]
    public function invokingReturnsTheViewHelperConfiguration(): void
    {
        $provider = new ConfigProvider();

        static::assertSame(['view_helpers' => $provider->getViewHelperConfig()], $provider());
    }

    #[Test]
    #[DataProvider('factoryProvider')]
    public function registersEachHelperWithItsFactory(string $helper, string $factory): void
    {
        static::assertSame($factory, (new ConfigProvider())->getViewHelperConfig()['factories'][$helper] ?? null);
    }
}
