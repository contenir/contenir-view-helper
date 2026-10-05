<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Stringable;

use function intdiv;
use function preg_replace;
use function strip_tags;
use function strlen;
use function substr;

/**
 * Shortens text to a maximum length, flattening headings and line breaks
 * to plain sentences first.
 *
 * @api
 */
final class Truncate extends AbstractHelper
{
    /**
     * @param int    $length     Maximum length including $etc; 0 returns an empty string.
     * @param bool   $strip      Strip HTML tags before measuring.
     * @param string $etc        Appended (or inserted, with $middle) where text was cut.
     * @param bool   $breakWords Cut mid-word instead of at the previous word boundary.
     * @param bool   $middle     Keep the start and end, cutting from the middle.
     */
    public function __invoke(
        string|int|float|Stringable|null $value,
        int $length = 125,
        bool $strip = false,
        string $etc = '...',
        bool $breakWords = false,
        bool $middle = false,
    ): string {
        if (0 === $length) {
            return '';
        }

        $value = (string) preg_replace(
            '/<h(\d+)[^>]*>(.*?)\.?<\/h\\1>/msi',
            replacement: '\\2. ',
            subject: (string) $value,
        );
        $value = (string) preg_replace('/<br[^>]*>/mi', replacement: "\n", subject: $value);
        $value = (string) preg_replace('/[\r\n]+/mi', replacement: ' ', subject: $value);
        if ($strip) {
            $value = strip_tags($value);
        }

        if (strlen($value) <= $length) {
            return $value;
        }

        $length -= strlen($etc);

        if ($middle) {
            $half = intdiv($length, num2: 2);

            return substr($value, offset: 0, length: $half) . $etc . substr($value, -$half);
        }

        if (! $breakWords) {
            $value = (string) preg_replace(
                '/\s+?(\S+)?$/',
                replacement: '',
                subject: substr($value, offset: 0, length: $length + 1),
            );
        }

        return substr($value, offset: 0, length: $length) . $etc;
    }
}
