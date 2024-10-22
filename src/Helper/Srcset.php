<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class Srcset extends AbstractHelper
{
    public function __invoke($filepath = null, array $sizes = []): string
    {
        $srcset                 = [];
        $parts                  = pathinfo($filepath);

        foreach ($sizes as $size) {
            $dimensions = explode('x', $size);
            $resizeDimensions = (count($dimensions) > 1) ? $size : sprintf('%sx', $dimensions[0]);

            $srcset[] = sprintf(
                "%s/%s_%s.%s %sw",
                $parts['dirname'],
                $parts['filename'],
                $resizeDimensions,
                $parts['extension'],
                $dimensions[0]
            );
        }

        return join(',', $srcset);
    }
}
