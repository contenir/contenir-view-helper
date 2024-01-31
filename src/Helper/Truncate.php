<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class Truncate extends AbstractHelper
{
    public function __invoke(
        $value,
        $length = 125,
        $strip = false,
        $etc = '...',
        $break_words = false,
        $middle = false
    ) {
        $html = '';

        if ($length == 0) {
            return $html;
        }

        $value = preg_replace('/<h(\d+)[^>]*>(.*?)\.?<\/h\\1>/msi', '\\2. ', (string)$value);
        $value = preg_replace('/<br[^>]*>/msi', "\n", $value);
        $value = preg_replace('/[\r\n]+/msi', ' ', $value);
        if ($strip) {
            $value = strip_tags($value);
        }

        if (strlen($value) > $length) {
            $length -= strlen($etc);
            if (! $break_words && ! $middle) {
                $value = preg_replace('/\s+?(\S+)?$/', '', substr($value, 0, $length + 1));
            }
            if (! $middle) {
                $html = substr($value, 0, $length) . $etc;
            } else {
                $html = substr($value, 0, $length / 2) . $etc . substr($value, -$length / 2);
            }
        } else {
            $html = $value;
        }

        return $html;
    }
}
