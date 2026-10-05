<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Integration\Helper;

use Contenir\View\Helper\Video;
use Contenir\View\Tests\Trait\PhpRendererTrait;
use Laminas\View\Helper\HeadLink;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function array_map;
use function array_values;
use function iterator_to_array;

#[CoversClass(Video::class)]
#[Group('integration')]
#[Group('view')]
final class VideoTest extends TestCase
{
    use PhpRendererTrait;

    private const string VIMEO = <<<'HTML'
        <iframe
            class="video"
            src="https://player.vimeo.com/video/123456?autoplay=0&mute=0&loop=0&title=0&byline=0&portrait=0"
            data-vimeo-portrait="false"
            data-vimeo-byline="false"
            data-vimeo-title="false"
            data-vimeo="1"
            allowfullscreen>
        </iframe>
        HTML;

    /**
     * @return array<string, array{string, array<string, mixed>, bool, string}>
     */
    public static function videoProvider(): array
    {
        return [
            'file autoplays muted on loop'  => [
                '/asset/hero.mp4',
                [],
                false,
                '<video class="video" playsinline preload="metadata" muted autoplay loop>'
                    . '<source src="&#x2F;asset&#x2F;hero.mp4"></video>',
            ],
            'file with controls'            => [
                '/asset/hero.mp4',
                ['videoClass' => 'clip', 'preload' => ''],
                true,
                '<video class="clip" playsinline controls><source src="&#x2F;asset&#x2F;hero.mp4"></video>',
            ],
            'vimeo embed'                   => ['https://vimeo.com/123456', [], false, self::VIMEO],
            'vimeo embed with wrapper'      => [
                'http://www.vimeo.com/123456',
                ['videoWrapperClass' => 'ratio ratio--16x9'],
                false,
                '<div class="ratio ratio--16x9">' . self::VIMEO . '</div>',
            ],
            'vimeo external file'           => [
                'https://player.vimeo.com/external/1.hd.mp4',
                [],
                false,
                '<video class="video" playsinline preload="metadata" muted autoplay loop>'
                    . '<source src="https&#x3A;&#x2F;&#x2F;player.vimeo.com&#x2F;external&#x2F;1.hd.mp4"></video>',
            ],
            'youtube embed ignores wrapper' => [
                'https://www.youtube.com/watch?v=abc123&t=10',
                ['videoWrapperClass' => 'ratio'],
                false,
                '<iframe class="video" src="https://www.youtube.com/embed/abc123?fs=1&amp;showinfo=0"></iframe>',
            ],
            'vimeo provider prefix'         => ['vimeo:123456', [], false, self::VIMEO],
            'prefix not at the start'       => [
                'my-youtube:abc',
                [],
                false,
                '<video class="video" playsinline preload="metadata" muted autoplay loop>'
                    . '<source src="my-youtube&#x3A;abc"></video>',
            ],
            'prefix spanning lines'         => [
                "vimeo:12\n34",
                [],
                false,
                '<video class="video" playsinline preload="metadata" muted autoplay loop>'
                    . '<source src="vimeo&#x3A;12&#x0A;34"></video>',
            ],
            'provider prefix is a file'     => [
                'cdn:clip.mp4',
                [],
                false,
                '<video class="video" playsinline preload="metadata" muted autoplay loop>'
                    . '<source src="cdn&#x3A;clip.mp4"></video>',
            ],
        ];
    }

    #[Test]
    public function aPosterIsShownAndPreloadedOnce(): void
    {
        $renderer = $this->createRenderer();
        $video    = $renderer->plugin(Video::class);

        $html = $video('/asset/hero.mp4', ['poster' => '/asset/hero.jpg']);
        $video('/asset/hero.mp4', ['poster' => '/asset/hero.jpg']);

        static::assertSame(
            [
                '<video class="video" playsinline poster="&#x2F;asset&#x2F;hero.jpg" preload="metadata" muted autoplay loop>'
                    . '<source src="&#x2F;asset&#x2F;hero.mp4"></video>',
                [['preload', '/asset/hero.jpg', 'image', 'high']],
            ],
            [$html, $this->headLinks($renderer->plugin(HeadLink::class))],
        );
    }

    #[Test]
    public function aPosterPreloadCanBeTurnedOff(): void
    {
        $renderer = $this->createRenderer();
        $renderer->plugin(Video::class)('/asset/hero.mp4', ['poster' => '/asset/hero.jpg', 'preloadPoster' => false]);

        static::assertSame([], $this->headLinks($renderer->plugin(HeadLink::class)));
    }

    #[Test]
    public function aPosterPreloadSitsAheadOfOtherLinks(): void
    {
        $renderer = $this->createRenderer();
        $headLink = $renderer->plugin(HeadLink::class);
        $headLink(['rel' => 'stylesheet', 'href' => '/style.css']);
        $headLink(['rel' => 'preload', 'href' => '/other.jpg', 'as' => 'image']);

        $renderer->plugin(Video::class)('/asset/hero.mp4', ['poster' => '/asset/hero.jpg']);

        static::assertSame(
            [
                ['preload',    '/asset/hero.jpg', 'image', 'high'],
                ['stylesheet', '/style.css',      null,    null],
                ['preload',    '/other.jpg',      'image', null],
            ],
            $this->headLinks($headLink),
        );
    }

    /**
     * @param array<string, mixed> $options
     */
    #[Test]
    #[DataProvider('videoProvider')]
    public function rendersTheVideo(string $path, array $options, bool $controls, string $expected): void
    {
        static::assertSame($expected, $this->createRenderer()->plugin(Video::class)($path, $options, $controls));
    }

    /**
     * @return list<array{mixed, mixed, mixed, mixed}>
     */
    private function headLinks(HeadLink $headLink): array
    {
        return array_map(
            static fn(object $link): array => [
                $link->rel ?? null,
                $link->href ?? null,
                $link->as ?? null,
                $link->fetchpriority ?? null,
            ],
            array_values(iterator_to_array($headLink->getContainer())),
        );
    }
}
