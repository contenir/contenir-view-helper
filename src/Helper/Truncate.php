<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class Truncate extends AbstractHelper
{
    public function __invoke(
        $string,
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

        $string = preg_replace('/<h(\d+)[^>]*>(.*?)\.?<\/h\\1>/msi', '\\2. ', $string);
        $string = preg_replace('/<br[^>]*>/msi', "\n", $string);
        $string = preg_replace('/[\r\n]+/msi', ' ', $string);
        if ($strip) {
            $string = strip_tags($string);
        }

        if (strlen($string) > $length) {
            $length -= strlen($etc);
            if (! $break_words && ! $middle) {
                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
            }
            if (! $middle) {
                $html = substr($string, 0, $length) . $etc;
            } else {
                $html = substr($string, 0, $length / 2) . $etc . substr($string, -$length / 2);
            }
        } else {
            $html = $string;
        }

        return $html;
    }
}
