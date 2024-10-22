<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class FileType extends AbstractHelper
{
    public function __invoke(
        $mimeType
    ): string {
        return match ($mimeType) {
            'application/pdf' => 'PDF',
            default => 'Document',
        };
    }
}
