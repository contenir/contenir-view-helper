<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Integration\Helper;

use Contenir\View\Helper\Image;
use Contenir\View\Tests\Trait\PhpRendererTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Image::class)]
#[Group('integration')]
#[Group('view')]
final class ImageTest extends TestCase
{
    use PhpRendererTrait;

    /**
     * @return array<string, array{array<string, mixed>, string, array<string, scalar|null>, array<string, scalar|null>, string}>
     */
    public static function imageProvider(): array
    {
        return [
            'local path on the CDN'       => [
                ['host' => 'cdn.example.com'],
                '/asset/hero.jpg?v=2',
                [],
                [],
                '<picture class="picture"><img src="https&#x3A;&#x2F;&#x2F;cdn.example.com&#x2F;asset&#x2F;hero.jpg&#x3F;width&#x3D;20&amp;auto_optimize&#x3D;high" '
                    . 'data-src="https&#x3A;&#x2F;&#x2F;cdn.example.com&#x2F;asset&#x2F;hero.jpg&#x3F;auto_optimize&#x3D;high" '
                    . 'class="img" alt="" data-lazyload></picture>',
            ],
            'CDN scheme and resize width' => [
                ['host' => 'cdn.example.com', 'method' => 'http'],
                '/asset/hero.jpg',
                ['resizeWidth' => 800, 'alt' => 'Hero', 'class' => 'hero'],
                ['class' => 'frame'],
                '<picture class="frame"><img src="http&#x3A;&#x2F;&#x2F;cdn.example.com&#x2F;asset&#x2F;hero.jpg&#x3F;width&#x3D;20&amp;auto_optimize&#x3D;high" '
                    . 'data-src="http&#x3A;&#x2F;&#x2F;cdn.example.com&#x2F;asset&#x2F;hero.jpg&#x3F;auto_optimize&#x3D;high&amp;width&#x3D;800" '
                    . 'class="hero" alt="Hero" data-lazyload></picture>',
            ],
            'local path without a CDN'    => [
                [],
                '/asset/hero.jpg',
                [],
                [],
                '<picture class="picture"><img src="&#x2F;asset&#x2F;hero.jpg&#x3F;width&#x3D;20&amp;auto_optimize&#x3D;high" '
                    . 'data-src="&#x2F;asset&#x2F;hero.jpg&#x3F;auto_optimize&#x3D;high" class="img" alt="" data-lazyload></picture>',
            ],
            'empty CDN host and path'     => [
                ['host' => '', 'method' => 42],
                '///',
                [],
                [],
                '<picture class="picture"><img src="&#x3F;width&#x3D;20&amp;auto_optimize&#x3D;high" '
                    . 'data-src="&#x3F;auto_optimize&#x3D;high" class="img" alt="" data-lazyload></picture>',
            ],
            'absolute URL as it is'       => [
                ['host' => 'cdn.example.com'],
                'https://images.example.org/a.jpg',
                [],
                [],
                '<picture class="picture"><img src="https&#x3A;&#x2F;&#x2F;images.example.org&#x2F;a.jpg" '
                    . 'data-src="https&#x3A;&#x2F;&#x2F;images.example.org&#x2F;a.jpg" class="img" alt="" data-lazyload></picture>',
            ],
        ];
    }

    /**
     * @param array<string, mixed>       $options
     * @param array<string, scalar|null> $attributes
     * @param array<string, scalar|null> $pictureAttributes
     */
    #[Test]
    #[DataProvider('imageProvider')]
    public function rendersTheLazyLoadedPicture(
        array $options,
        string $path,
        array $attributes,
        array $pictureAttributes,
        string $expected,
    ): void {
        $image = new Image($options);
        $image->setView($this->createRenderer());

        static::assertSame($expected, $image($path, $attributes, $pictureAttributes));
    }
}
