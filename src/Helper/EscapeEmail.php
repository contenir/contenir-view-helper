<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class EscapeEmail extends AbstractHelper
{
    public function __invoke($email, $mailto = false): string
    {
        $address = '';
        $escape = '<!-- . -->&#x%s;';

        if ($mailto) {
            $escape = '%%%s';
        }

        for ($x = 0; $x < strlen($email); $x++) {
            $address .= sprintf($escape, bin2hex($email[$x]));
        }

        if ($mailto) {
            $address = "&#109;&#97;&#105;&#108;&#116;&#111;&#58;" . $address;
        }

        return $address;
    }
}
