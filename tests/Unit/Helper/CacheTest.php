<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit\Helper;

use Contenir\View\Helper\Cache;
use Laminas\Cache\Exception\RuntimeException as CacheRuntimeException;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Renderer\RendererInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Cache::class)]
#[Group('unit')]
final class CacheTest extends TestCase
{
    /**
     * @return array<string, array{string|null, int|string|null}>
     */
    public static function incompleteArgumentsProvider(): array
    {
        return [
            'no arguments' => [null, null],
            'no key'       => ['partial/nav', null],
            'no script'    => [null, 'nav'],
        ];
    }

    #[Test]
    public function capturesAndStoresABlockBetweenStartAndEnd(): void
    {
        $storage = $this->createMock(StorageInterface::class);
        $storage->method('getItem')->willReturn(null);
        $storage->expects(static::once())
            ->method('setItem')
            ->with('footer', '<footer>fresh</footer>')
            ->willReturn(true);

        $helper = new Cache($storage);
        $this->expectOutputString('<footer>fresh</footer>');

        $started = $helper->start('footer');
        echo '<footer>fresh</footer>';

        static::assertSame([false, true], [$started, $helper->end()]);
    }

    #[Test]
    public function endingWithoutStartingFails(): void
    {
        $this->expectException(CacheRuntimeException::class);
        $this->expectExceptionMessage('Output cache not started');

        (new Cache($this->createStub(StorageInterface::class)))->end();
    }

    #[Test]
    public function refusesToRenderWithoutARenderer(): void
    {
        $storage = $this->createStub(StorageInterface::class);
        $storage->method('hasItem')->willReturn(false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The cache helper needs a renderer to render a script');

        (new Cache($storage))('partial/nav', key: 'nav');
    }

    #[Test]
    public function rendersAndStoresTheScriptOnAMiss(): void
    {
        $stored  = [];
        $storage = $this->createStub(StorageInterface::class);
        $storage->method('hasItem')->willReturn(false);
        $storage->method('setItem')
            ->willReturnCallback(static function (string $key, mixed $value) use (&$stored): bool {
                $stored[$key] = $value;

                return true;
            });
        $storage->method('getItem')
            ->willReturnCallback(static function (string $key) use (&$stored): mixed {
                return $stored[$key] ?? null;
            });

        $renderer = $this->createStub(RendererInterface::class);
        $renderer->method('render')->willReturn('<nav>fresh</nav>');

        $helper = new Cache($storage);
        $helper->setView($renderer);

        static::assertSame(['<nav>fresh</nav>', ['42' => '<nav>fresh</nav>']], [
            $helper('partial/nav', key: 42),
            $stored,
        ]);
    }

    #[Test]
    public function returnsCachedOutputWithoutRendering(): void
    {
        $storage = $this->createMock(StorageInterface::class);
        $storage->method('hasItem')->with('main_nav')->willReturn(true);
        $storage->expects(static::never())->method('setItem');
        $storage->method('getItem')->with('main_nav')->willReturn('<nav>cached</nav>');

        $renderer = $this->createMock(RendererInterface::class);
        $renderer->expects(static::never())->method('render');

        $helper = new Cache($storage);
        $helper->setView($renderer);

        static::assertSame('<nav>cached</nav>', $helper('partial/nav', key: 'Main  Nav!'));
    }

    #[Test]
    #[DataProvider('incompleteArgumentsProvider')]
    public function returnsItselfWithoutBothScriptAndKey(?string $script, int|string|null $key): void
    {
        $helper = new Cache($this->createStub(StorageInterface::class));

        static::assertSame($helper, $helper($script, $key));
    }

    #[Test]
    public function startEchoesACachedBlockAndReportsAHit(): void
    {
        $storage = $this->createStub(StorageInterface::class);
        $storage->method('getItem')
            ->willReturnCallback(static function (string $key, ?bool &$success = null): string {
                $success = 'footer' === $key;

                return '<footer>cached</footer>';
            });

        $this->expectOutputString('<footer>cached</footer>');

        static::assertTrue((new Cache($storage))->start('Footer'));
    }

    #[Test]
    public function treatsANonStringCacheEntryAsEmpty(): void
    {
        $storage = $this->createStub(StorageInterface::class);
        $storage->method('hasItem')->willReturn(true);
        $storage->method('getItem')->willReturn(['not', 'output']);

        static::assertSame('', (new Cache($storage))('partial/nav', key: 'nav'));
    }
}
