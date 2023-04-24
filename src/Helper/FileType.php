<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class FileType extends AbstractHelper
{
    public function __invoke(
        $mimeType
    ) {
        switch ($mimeType) {
            case 'application/pdf':
                $type = 'PDF';
                break;

            default:
                $type = 'Document';
                break;
        }

        return $type;
    }
}
