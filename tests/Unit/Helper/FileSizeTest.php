<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit\Helper;

use Contenir\View\Helper\FileSize;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FileSize::class)]
#[Group('unit')]
final class FileSizeTest extends TestCase
{
    /**
     * @return array<string, array{int|float|string, int, string, string}>
     */
    public static function sizeProvider(): array
    {
        return [
            'zero bytes'                  => [0, 2, FileSize::SYSTEM_METRIC, '0B'],
            'bytes below one kilobyte'    => [999, 2, FileSize::SYSTEM_METRIC, '999B'],
            'whole kilobytes drop places' => [1000, 2, FileSize::SYSTEM_METRIC, '1kB'],
            'fractional megabytes'        => [1_500_000, 2, FileSize::SYSTEM_METRIC, '1.50MB'],
            'custom decimal places'       => [1_234_567, 1, FileSize::SYSTEM_METRIC, '1.2MB'],
            'numeric string'              => ['2500', 2, FileSize::SYSTEM_METRIC, '2.50kB'],
            'float bytes'                 => [1536.0, 2, FileSize::SYSTEM_BINARY, '1.50KiB'],
            'negative size'               => [-1500, 2, FileSize::SYSTEM_METRIC, '-1.50kB'],
            'binary kibibyte'             => [1024, 2, FileSize::SYSTEM_BINARY, '1KiB'],
            'binary below one kibibyte'   => [1000, 2, FileSize::SYSTEM_BINARY, '1000B'],
            'binary gibibytes'            => [3 * (1024 ** 3), 2, FileSize::SYSTEM_BINARY, '3GiB'],
            'beyond yottabytes'           => [1e30, 0, FileSize::SYSTEM_METRIC, '1000000YB'],
            'unknown system has no unit'  => [1500, 2, 'imperial', '1.50'],
        ];
    }

    #[Test]
    #[DataProvider('sizeProvider')]
    public function formatsTheSize(int|float|string $bytes, int $decimals, string $system, string $expected): void
    {
        static::assertSame($expected, (new FileSize())($bytes, $decimals, $system));
    }
}
