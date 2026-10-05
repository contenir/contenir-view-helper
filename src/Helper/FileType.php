<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Turns a MIME type into a short label for download links.
 *
 * @api
 */
final class FileType extends AbstractHelper
{
    public function __invoke(?string $mimeType): string
    {
        return match ($mimeType) {
            'application/pdf' => 'PDF',
            default           => 'Document',
        };
    }
}
