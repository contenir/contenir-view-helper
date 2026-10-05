<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Trait;

use Contenir\View\ConfigProvider;
use Laminas\Form\ConfigProvider as FormConfigProvider;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\ServerUrl;
use Laminas\View\HelperPluginManager;
use Laminas\View\Renderer\PhpRenderer;

use function array_merge_recursive;

/**
 * A PhpRenderer wired with the laminas-form helpers and this package's
 * helpers, serving https://www.example.com with an HTML5 doctype.
 */
trait PhpRendererTrait
{
    /**
     * @param array<string, mixed> $services Extra application services, e.g. "config".
     */
    private function createRenderer(array $services = []): PhpRenderer
    {
        $helpers = array_merge_recursive(
            (new FormConfigProvider())->getViewHelperConfig(),
            (new ConfigProvider())->getViewHelperConfig(),
        );
        $renderer = new PhpRenderer();
        $renderer->setHelperPluginManager(new HelperPluginManager(new ServiceManager([
            'services' => $services,
        ]), $helpers));

        $renderer->plugin(Doctype::class)->setDoctype(Doctype::HTML5);
        $renderer->plugin(ServerUrl::class)->setScheme('https')->setHost('www.example.com');

        return $renderer;
    }
}
