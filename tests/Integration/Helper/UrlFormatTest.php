<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Integration\Helper;

use Contenir\View\Helper\PHPViewTrait;
use Contenir\View\Helper\UrlFormat;
use Contenir\View\Tests\Trait\PhpRendererTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(UrlFormat::class)]
#[CoversTrait(PHPViewTrait::class)]
#[Group('integration')]
#[Group('view')]
final class UrlFormatTest extends TestCase
{
    use PhpRendererTrait;

    /**
     * @return array<string, array{string|null, string|null, string}>
     */
    public static function urlProvider(): array
    {
        $all = '%scheme%%userinfo%@%host%:%port%%path%%query%%fragment%%unknown%';

        return [
            'default format keeps scheme, host and path' => [
                'https://user@www.example.org:8080/page?q=1#top',
                null,
                'https://www.example.org/page',
            ],
            'every part'                                 => [
                'https://user@www.example.org:8080/page?q=1#top',
                $all,
                'https://user@www.example.org:8080/page?q=1#top',
            ],
            'missing parts render empty'                 => [
                'https://www.example.org',
                $all,
                'https://@www.example.org:443',
            ],
            'no port'                                    => ['file:///tmp/a.txt', '[%port%]%path%', '[]/tmp/a.txt'],
            'scheme-less URL is assumed http'            => ['example.org/page', null, 'http://example.org/page'],
            'root-relative URL uses the current server'  => ['/about', null, 'https://www.example.com/about'],
            'scheme later in a scheme-less URL'          => [
                'example.org/go?to=https://x.org',
                null,
                'http://example.org/go',
            ],
            'host only'                                  => ['https://www.example.org/a', '%host%', 'www.example.org'],
            'empty URL'                                  => ['', null, ''],
            'no URL'                                     => [null, null, ''],
            'unsupported scheme'                         => ['gopher://example.org', null, ''],
        ];
    }

    #[Test]
    #[DataProvider('urlProvider')]
    public function formatsTheUrl(?string $url, ?string $format, string $expected): void
    {
        static::assertSame($expected, $this->createRenderer()->plugin(UrlFormat::class)($url, $format));
    }
}
