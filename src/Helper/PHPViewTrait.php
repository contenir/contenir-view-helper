<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\View\Exception\RuntimeException;
use Laminas\View\Renderer\PhpRenderer;

trait PHPViewTrait
{
    protected function getPHPView(): PhpRenderer
    {
        $view = $this->getView();
        if (! $view instanceof PhpRenderer) {
            throw new RuntimeException('This plugin requires a PHP View Renderer implementation');
        }

        return $view;
    }
}