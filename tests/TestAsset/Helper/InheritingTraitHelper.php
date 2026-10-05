<?php

declare(strict_types=1);

namespace Contenir\View\Tests\TestAsset\Helper;

use Laminas\View\Renderer\PhpRenderer;

/**
 * Calls PHPViewTrait's methods from a subclass of the helper that uses it.
 */
final class InheritingTraitHelper extends AbstractTraitHelper
{
    /**
     * @return array{string, string, PhpRenderer}
     */
    public function __invoke(string $value): array
    {
        return [$this->escapeHtml($value), $this->escapeHtmlAttr($value), $this->getPHPView()];
    }
}
