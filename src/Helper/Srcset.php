<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;

use function array_map;
use function explode;
use function implode;
use function pathinfo;
use function sprintf;
use function str_contains;

/**
 * Builds a srcset attribute value from pre-resized derivatives named
 * "<dir>/<name>_<width>x[<height>].<ext>".
 *
 * @api
 */
final class Srcset extends AbstractHelper
{
    /**
     * @param list<int|string> $sizes Widths ("800") or dimensions ("800x600").
     */
    public function __invoke(?string $filepath = null, array $sizes = []): string
    {
        $parts     = pathinfo($filepath ?? '');
        $dirname   = $parts['dirname'] ?? '.';
        $extension = null === ($parts['extension'] ?? null) ? '' : ".{$parts['extension']}";

        return implode(',', array_map(
            static function (int|string $size) use ($parts, $dirname, $extension): string {
                $size  = (string) $size;
                $width = explode('x', $size)[0];

                return sprintf(
                    '%s/%s_%s%s %sw',
                    $dirname,
                    $parts['filename'],
                    str_contains($size, 'x') ? $size : "{$width}x",
                    $extension,
                    $width,
                );
            },
            $sizes,
        ));
    }
}
