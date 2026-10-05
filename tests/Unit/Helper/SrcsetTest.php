<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit\Helper;

use Contenir\View\Helper\Srcset;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Srcset::class)]
#[Group('unit')]
final class SrcsetTest extends TestCase
{
    /**
     * @return array<string, array{string|null, list<int|string>, string}>
     */
    public static function srcsetProvider(): array
    {
        return [
            'widths'         => [
                '/asset/hero.jpg',
                [800, '1600'],
                '/asset/hero_800x.jpg 800w,/asset/hero_1600x.jpg 1600w',
            ],
            'dimensions'     => ['/asset/hero.jpg', ['800x600'], '/asset/hero_800x600.jpg 800w'],
            'no extension'   => ['/asset/hero', [400], '/asset/hero_400x 400w'],
            'bare file name' => ['hero.png', [400], './hero_400x.png 400w'],
            'no sizes'       => ['/asset/hero.jpg', [], ''],
            'no path'        => [null, [], ''],
        ];
    }

    #[Test]
    #[DataProvider('srcsetProvider')]
    public function buildsTheSrcset(?string $filepath, array $sizes, string $expected): void
    {
        static::assertSame($expected, (new Srcset())($filepath, $sizes));
    }
}
