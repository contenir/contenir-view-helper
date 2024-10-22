<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class FileSize extends AbstractHelper
{
    public const SYSTEM_BINARY = 'binary';
    public const SYSTEM_METRIC = 'metric';

    public function __invoke(
        $bytes,
        $decimals = 2,
        $system = self::SYSTEM_METRIC
    ): string {
        $mod = ($system === self::SYSTEM_BINARY) ? 1024 : 1000;

        $units = [
            self::SYSTEM_BINARY => [
                'B',
                'KiB',
                'MiB',
                'GiB',
                'TiB',
                'PiB',
                'EiB',
                'ZiB',
                'YiB',
            ],
            self::SYSTEM_METRIC => [
                'B',
                'kB',
                'MB',
                'GB',
                'TB',
                'PB',
                'EB',
                'ZB',
                'YB',
            ],
        ];

        $factor = floor((strlen($bytes) - 1) / 3);
        $value = $bytes / pow($mod, $factor);

        if (floor($value) == sprintf("%.{$decimals}f", $value)) {
            $decimals = 0;
        }

        return sprintf(
            "%.{$decimals}f%s",
            $value,
            $units[$system][$factor] ?? ''
        );
    }
}
