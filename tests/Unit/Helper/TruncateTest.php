<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit\Helper;

use Contenir\View\Helper\Truncate;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Stringable;

#[CoversClass(Truncate::class)]
#[Group('unit')]
final class TruncateTest extends TestCase
{
    private const string FOX = 'The quick brown fox jumps';

    /**
     * @return array<string, array{array{string|null, int, bool, string, bool, bool}, string}>
     */
    public static function truncateProvider(): array
    {
        return [
            'zero length'               => [['Hello world', 0, false, '...', false, false], ''],
            'null value'                => [[null, 10, false, '...', false, false], ''],
            'short text unchanged'      => [['Hello world', 20, false, '...', false, false], 'Hello world'],
            'cuts at a word boundary'   => [[self::FOX, 15, false, '...', false, false], 'The quick...'],
            'breaks words when asked'   => [[self::FOX, 15, false, '...', true, false], 'The quick br...'],
            'custom ellipsis'           => [[self::FOX, 16, false, '~', false, false], 'The quick brown~'],
            'cuts from the middle'      => [['abcdefghijklmnopqrstuvwxyz', 10, false, '..', false, true], 'abcd..wxyz'],
            'odd middle length'         => [['abcdefghijklmnopqrstuvwxyz', 11, false, '..', false, true], 'abcd..wxyz'],
            'headings become sentences' => [['<h2>Title</h2>Body', 50, false, '...', false, false], 'Title. Body'],
            'line breaks become spaces' => [["One<br>Two\r\nThree", 50, false, '...', false, false], 'One Two Three'],
            'strips tags when asked'    => [
                ['<p>Hello <b>world</b></p>', 50, true, '...', false, false],
                'Hello world',
            ],
            'keeps tags unless asked'   => [['<b>Hi</b>', 50, false, '...', false, false], '<b>Hi</b>'],
        ];
    }

    #[Test]
    public function acceptsStringableValues(): void
    {
        $value = new class implements Stringable {
            public function __toString(): string
            {
                return 'Stringable text';
            }
        };

        static::assertSame('Stringable text', (new Truncate())($value));
    }

    /**
     * @param array{string|null, int, bool, string, bool, bool} $arguments Value, length, strip, etc,
     *                                                                     breakWords and middle.
     */
    #[Test]
    #[DataProvider('truncateProvider')]
    public function truncatesTheText(array $arguments, string $expected): void
    {
        static::assertSame($expected, (new Truncate())(...$arguments));
    }
}
