<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Integration\Helper;

use Contenir\View\Helper\SocialLink;
use Contenir\View\Tests\Trait\PhpRendererTrait;
use Contenir\View\Tests\Trait\TemporaryDirectoryTrait;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function chdir;
use function getcwd;

#[CoversClass(SocialLink::class)]
#[Group('integration')]
#[Group('view')]
final class SocialLinkTest extends TestCase
{
    use PhpRendererTrait;
    use TemporaryDirectoryTrait;

    private string $originalDirectory;

    /**
     * @return array<string, array{string, string|null, string}>
     */
    public static function linkProvider(): array
    {
        return [
            'handle'             => [
                'instagram',
                'contenir',
                '<a aria-label="Instagram" class="navbar__link" target="_blank" href="https://instagram.com/contenir">Instagram</a>',
            ],
            'full profile URL'   => [
                'linkedin',
                'https://www.linkedin.com/in/contenir',
                '<a aria-label="LinkedIn" class="navbar__link" target="_blank" href="https://linkedin.com/in/contenir">LinkedIn</a>',
            ],
            'settings-style id'  => [
                'social_facebook',
                'http://facebook.com/contenir',
                '<a aria-label="Facebook" class="navbar__link" target="_blank" href="https://facebook.com/contenir">Facebook</a>',
            ],
            'unknown network'    => ['myspace', 'contenir', ''],
            'quotes are escaped' => [
                'vimeo',
                'a"b',
                '<a aria-label="Vimeo" class="navbar__link" target="_blank" href="https://vimeo.com/a&quot;b">Vimeo</a>',
            ],
        ];
    }

    #[Test]
    public function inlinesTheNetworkIconWhenAsked(): void
    {
        $this->writeFile('public/asset/icon/icon-youtube.svg', '<svg/>');

        static::assertSame(
            '<a aria-label="Youtube" class="social" target="_blank" href="https://youtube.com/channel/abc"><svg/></a>',
            $this->createRenderer()->plugin(SocialLink::class)('youtube', link: 'abc', options: [
                'icon'       => true,
                'link_class' => 'social',
            ]),
        );
    }

    #[Test]
    public function rendersAnEmptyLinkWhenTheIconIsMissing(): void
    {
        static::assertSame(
            '<a aria-label="Medium" class="navbar__link" target="_blank" href="https://medium.com/@abc"></a>',
            $this->createRenderer()->plugin(SocialLink::class)('medium', link: 'abc', options: ['icon' => true]),
        );
    }

    #[Test]
    #[DataProvider('linkProvider')]
    public function rendersTheProfileLink(string $socialId, ?string $link, string $expected): void
    {
        static::assertSame($expected, $this->createRenderer()->plugin(SocialLink::class)($socialId, $link));
    }

    #[Override]
    protected function setUp(): void
    {
        $this->setUpTemporaryDirectory();
        $this->originalDirectory = (string) getcwd();
        chdir($this->tmpDir);
    }

    #[Override]
    protected function tearDown(): void
    {
        chdir($this->originalDirectory);
        $this->tearDownTemporaryDirectory();
    }
}
