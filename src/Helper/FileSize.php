<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;

use function abs;
use function floor;
use function round;
use function sprintf;

/**
 * Formats a byte count as a human-readable size: "1.5MB", or "1.5MiB" in
 * the binary system.
 *
 * @api
 */
class FileSize extends AbstractHelper
{
    public const string SYSTEM_BINARY = 'binary';
    public const string SYSTEM_METRIC = 'metric';

    private const int MAX_FACTOR = 8;

    private const array UNITS = [
        self::SYSTEM_BINARY => ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'],
        self::SYSTEM_METRIC => ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
    ];

    /**
     * @param int|float|numeric-string $bytes
     * @param int                      $decimals Decimal places, dropped when the value is whole.
     * @param string                   $system   One of the SYSTEM_* constants. Any other value
     *                                           scales by 1000 and omits the unit.
     */
    public function __invoke(int|float|string $bytes, int $decimals = 2, string $system = self::SYSTEM_METRIC): string
    {
        $base   = self::SYSTEM_BINARY === $system ? 1024 : 1000;
        $units  = self::UNITS[$system] ?? [];
        $value  = (float) $bytes;
        $factor = 0;

        while (abs($value) >= $base && $factor < self::MAX_FACTOR) {
            $value /= $base;
            ++$factor;
        }

        if (round($value, $decimals) === floor($value)) {
            $decimals = 0;
        }

        return sprintf("%.{$decimals}f%s", $value, $units[$factor] ?? '');
    }
}
