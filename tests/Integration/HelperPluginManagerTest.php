<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Integration;

use Contenir\View\ConfigProvider;
use Contenir\View\Helper;
use Contenir\View\Helper\Factory\AclFactory;
use Contenir\View\Tests\Trait\PhpRendererTrait;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Permissions\Acl\Acl;
use Laminas\View\Renderer\PhpRenderer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function sprintf;

#[CoversClass(ConfigProvider::class)]
#[Group('integration')]
#[Group('view')]
final class HelperPluginManagerTest extends TestCase
{
    use PhpRendererTrait;

    /**
     * @return array<string, array{string, class-string}>
     */
    public static function aliasProvider(): array
    {
        $cases = [];
        foreach ((new ConfigProvider())->getViewHelperConfig()['aliases'] as $alias => $helper) {
            $cases[$alias] = [$alias, $helper];
        }

        return $cases;
    }

    #[Test]
    public function iconHelperReceivesTheConfiguredClass(): void
    {
        static::assertSame(
            'icon',
            $this->renderer()
                ->plugin(Helper\Icon::class)
                ->getClass(),
        );
    }

    #[Test]
    #[DataProvider('aliasProvider')]
    public function resolvesEveryAliasToItsHelper(string $alias, string $helper): void
    {
        static::assertInstanceOf($helper, $this->renderer()->plugin($alias));
    }

    #[Test]
    public function viewScriptsCallHelpersByAlias(): void
    {
        $renderer = $this->renderer();

        static::assertSame(
            sprintf('%s|%s', 'PDF', 'https://www.example.com/about'),
            sprintf('%s|%s', $renderer->fileType('application/pdf'), $renderer->urlFormat('/about')),
        );
    }

    private function renderer(): PhpRenderer
    {
        return $this->createRenderer([
            'config'            => [
                'view_cdn'           => ['host' => 'cdn.example.com'],
                'settings'           => ['site_name' => 'Contenir'],
                'view_helper_config' => ['icon' => ['class' => 'icon']],
            ],
            'ViewCache'         => $this->createStub(StorageInterface::class),
            AclFactory::SERVICE => new Acl(),
        ]);
    }
}
