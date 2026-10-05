<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit\Helper\Factory;

use Contenir\View\Helper\Factory\ImageFactory;
use Contenir\View\Helper\Image;
use Contenir\View\Tests\TestAsset\Container\InMemoryContainer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ImageFactory::class)]
#[Group('unit')]
final class ImageFactoryTest extends TestCase
{
    #[Test]
    public function buildsTheHelper(): void
    {
        static::assertInstanceOf(
            Image::class,
            (new ImageFactory())(new InMemoryContainer(['config' => ['view_cdn' => ['host' => 'cdn.example.com']]])),
        );
    }
}
