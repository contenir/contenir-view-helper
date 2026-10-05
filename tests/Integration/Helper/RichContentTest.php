<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Integration\Helper;

use Contenir\View\Helper\RichContent;
use Contenir\View\Tests\Trait\PhpRendererTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Stringable;

#[CoversClass(RichContent::class)]
#[Group('integration')]
#[Group('view')]
final class RichContentTest extends TestCase
{
    use PhpRendererTrait;

    /**
     * @return array<string, array{string|null, array<string, mixed>, string}>
     */
    public static function contentProvider(): array
    {
        return [
            'no content'                      => [null, [], ''],
            'single column passes through'    => ['<p>One</p>', [], '<p>One</p>'],
            'two columns'                     => [
                '<p>Left</p><p>__COL__</p><p>Right</p>',
                [],
                '<section class="grid grid--content"><div class="grid__col grid__col--2"><p>Left</p></div>'
                    . '<div class="grid__col grid__col--2"><p>Right</p></div></section>',
            ],
            'sections are laid out apart'     => [
                '<p>Intro</p><p>__SECTION__</p><p>A</p><p>__COL__</p><p>B</p>',
                [],
                '<p>Intro</p><section class="grid grid--content"><div class="grid__col grid__col--2"><p>A</p></div>'
                    . '<div class="grid__col grid__col--2"><p>B</p></div></section>',
            ],
            'unmapped count uses the default' => [
                'A<p>__COL__</p>B<p>__COL__</p>C<p>__COL__</p>D<p>__COL__</p>E',
                [],
                '<section class="grid grid--content"><div class="grid__col">A</div><div class="grid__col">B</div>'
                    . '<div class="grid__col">C</div><div class="grid__col">D</div><div class="grid__col">E</div></section>',
            ],
            'custom template'                 => [
                'A<p>__COL__</p>B',
                [
                    'outerTag'    => 'div',
                    'outerClass'  => 'row "x"',
                    'innerTag'    => 'div',
                    'innerClass'  => '',
                    'columnTag'   => '',
                    'columnClass' => [],
                ],
                '<div class="row &quot;x&quot;"><div>AB</div></div>',
            ],
        ];
    }

    #[Test]
    public function acceptsStringableContent(): void
    {
        $content = new class implements Stringable {
            public function __toString(): string
            {
                return '<p>One</p>';
            }
        };

        static::assertSame('<p>One</p>', $this->createRenderer()->plugin(RichContent::class)($content));
    }

    /**
     * @param array<string, mixed> $template
     */
    #[Test]
    #[DataProvider('contentProvider')]
    public function laysOutTheContent(?string $content, array $template, string $expected): void
    {
        static::assertSame($expected, $this->createRenderer()->plugin(RichContent::class)($content, $template));
    }
}
