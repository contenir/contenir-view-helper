<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Integration\Helper;

use Contenir\View\Helper\Icon;
use Contenir\View\Tests\Trait\TemporaryDirectoryTrait;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function mkdir;

#[CoversClass(Icon::class)]
#[Group('integration')]
final class IconTest extends TestCase
{
    use TemporaryDirectoryTrait;

    #[Test]
    public function aDirectoryIsNotAnIcon(): void
    {
        mkdir("{$this->tmpDir}/icons/folder.svg");

        static::assertSame('', $this->helper()('folder'));
    }

    #[Test]
    public function aMissingIconIsEmpty(): void
    {
        mkdir("{$this->tmpDir}/other");

        static::assertSame(
            ['', ''],
            [$this->helper()('missing'), $this->helper()('arrow', ['base_path' => "{$this->tmpDir}/other"])],
        );
    }

    #[Test]
    public function inlinesTheIconFromTheBasePath(): void
    {
        static::assertSame('<svg id="arrow"/>', $this->helper()('arrow'));
    }

    #[Test]
    public function perCallOptionsOverrideTheDefaultsForThatCallOnly(): void
    {
        $helper = $this->helper();

        static::assertSame(
            ['<span class="icon icon--png"><svg id="png"/></span>', '<svg id="arrow"/>'],
            [
                $helper('arrow', ['tag' => 'span', 'class' => 'icon icon--png', 'extension' => 'png.svg']),
                $helper('arrow'),
            ],
        );
    }

    #[Test]
    public function wrapsTheIconWhenAClassIsSet(): void
    {
        $helper = $this->helper()->setClass('icon');

        static::assertSame('<i class="icon"><svg id="arrow"/></i>', $helper('arrow'));
    }

    #[Override]
    protected function setUp(): void
    {
        $this->setUpTemporaryDirectory();
        $this->writeFile('icons/arrow.svg', '<svg id="arrow"/>');
        $this->writeFile('icons/arrow.png.svg', '<svg id="png"/>');
    }

    #[Override]
    protected function tearDown(): void
    {
        $this->tearDownTemporaryDirectory();
    }

    private function helper(): Icon
    {
        return (new Icon())->setBasePath("{$this->tmpDir}/icons");
    }
}
