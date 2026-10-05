<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit\Helper;

use Contenir\View\Helper\PHPViewTrait;
use Contenir\View\Helper\UrlFormat;
use Contenir\View\Tests\TestAsset\Helper\InheritingTraitHelper;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\Renderer\RendererInterface;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversTrait(PHPViewTrait::class)]
#[Group('unit')]
final class PHPViewTraitTest extends TestCase
{
    #[Test]
    public function itsMethodsAreAvailableToSubclassesOfTheHelperUsingIt(): void
    {
        $renderer = $this->createStub(PhpRenderer::class);
        $helper   = new InheritingTraitHelper();
        $helper->setView($renderer);

        static::assertSame(['a &amp; b', 'a&#x20;&amp;&#x20;b', $renderer], $helper('a & b'));
    }

    #[Test]
    public function requiresAView(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This plugin requires a PHP View Renderer implementation');

        (new UrlFormat())('/about');
    }

    #[Test]
    public function requiresThePhpRenderer(): void
    {
        $helper = new UrlFormat();
        $helper->setView($this->createStub(RendererInterface::class));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This plugin requires a PHP View Renderer implementation');

        $helper('/about');
    }
}
