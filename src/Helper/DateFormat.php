<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use DateTime;
use Exception;
use Laminas\View\Helper\AbstractHelper;

/**
 * Formats a date string, returning null when it cannot be parsed.
 *
 * @api
 */
final class DateFormat extends AbstractHelper
{
    private string $format = 'd M Y';

    /**
     * @param string|null $datetime Any string DateTime understands; null means now.
     * @param string|null $format   A date() format; null uses the helper default.
     */
    public function __invoke(?string $datetime = null, ?string $format = null): ?string
    {
        try {
            return (new DateTime($datetime ?? 'now'))->format($format ?? $this->format);
        } catch (Exception) {
            return null;
        }
    }
}
