<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit\Helper\Factory;

use Contenir\View\Helper\Factory\CacheFactory;
use Contenir\View\Tests\TestAsset\Container\InMemoryContainer;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CacheFactory::class)]
#[Group('unit')]
final class CacheFactoryTest extends TestCase
{
    #[Test]
    public function buildsTheHelperOnTheViewCacheStorage(): void
    {
        $storage = $this->createStub(StorageInterface::class);
        $storage->method('hasItem')->willReturn(true);
        $storage->method('getItem')->willReturn('cached');

        $helper = (new CacheFactory())(new InMemoryContainer(['ViewCache' => $storage]));

        static::assertSame('cached', $helper('partial/nav', key: 'nav'));
    }

    #[Test]
    public function rejectsAServiceThatIsNotACacheStorage(): void
    {
        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionMessage(
            'Service "ViewCache" must implement Laminas\Cache\Storage\StorageInterface, string given',
        );

        (new CacheFactory())(new InMemoryContainer([CacheFactory::SERVICE => 'not a cache']));
    }
}
