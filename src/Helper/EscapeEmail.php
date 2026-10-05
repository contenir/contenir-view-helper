<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;

use function array_map;
use function bin2hex;
use function implode;
use function sprintf;
use function str_split;

/**
 * Obfuscates an email address against simple harvesters: as HTML entities
 * for display, or percent-encoded behind an entity-encoded "mailto:" for
 * an href.
 *
 * @api
 */
final class EscapeEmail extends AbstractHelper
{
    private const string MAILTO = '&#109;&#97;&#105;&#108;&#116;&#111;&#58;';

    public function __invoke(string $email, bool $mailto = false): string
    {
        $escape = $mailto ? '%%%s' : '<!-- . -->&#x%s;';

        $address = implode('', array_map(
            static fn(string $char): string => sprintf($escape, bin2hex($char)),
            str_split($email),
        ));

        return $mailto ? self::MAILTO . $address : $address;
    }
}
