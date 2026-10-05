<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit\Helper;

use Contenir\View\Helper\DateFormat;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(DateFormat::class)]
#[Group('unit')]
final class DateFormatTest extends TestCase
{
    /**
     * @return array<string, array{string, string|null, string|null}>
     */
    public static function dateProvider(): array
    {
        return [
            'default format'     => ['2024-03-05 10:00:00', null, '05 Mar 2024'],
            'custom format'      => ['2024-03-05 10:00:00', 'Y-m-d', '2024-03-05'],
            'unparseable string' => ['not a date', null, null],
        ];
    }

    #[Test]
    public function formatsNowWhenNoDateIsGiven(): void
    {
        static::assertMatchesRegularExpression('/^\d{2} [A-Z][a-z]{2} \d{4}$/', (string) (new DateFormat())());
    }

    #[Test]
    #[DataProvider('dateProvider')]
    public function formatsTheDate(string $datetime, ?string $format, ?string $expected): void
    {
        static::assertSame($expected, (new DateFormat())($datetime, $format));
    }
}
