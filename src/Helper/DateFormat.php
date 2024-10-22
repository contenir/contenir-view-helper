<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use DateTime;
use Exception;

class DateFormat extends AbstractHelper
{
    protected string $format = 'd M Y';

    public function __invoke($datetime = null, $format = null): ?string
    {
        if ($format === null) {
            $format = $this->format;
        }

        try {
            $date = (new DateTime($datetime))->format($format);
        } catch (Exception) {
            $date = null;
        }

        return $date;
    }
}
