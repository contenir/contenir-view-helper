<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\Escaper\Escaper;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Renderer\PhpRenderer;

/**
 * Access to the PhpRenderer for helpers that render through other view
 * helpers, and UTF-8 escaping for the markup they build.
 *
 * @api
 *
 * @require-extends AbstractHelper
 */
trait PHPViewTrait
{
    protected function escapeHtml(string $value): string
    {
        return (new Escaper())->escapeHtml($value);
    }

    protected function escapeHtmlAttr(string $value): string
    {
        return (new Escaper())->escapeHtmlAttr($value);
    }

    /**
     * @throws RuntimeException when the helper is not attached to a PhpRenderer.
     */
    protected function getPHPView(): PhpRenderer
    {
        $view = $this->getView();
        if (! $view instanceof PhpRenderer) {
            throw new RuntimeException('This plugin requires a PHP View Renderer implementation');
        }

        return $view;
    }
}
