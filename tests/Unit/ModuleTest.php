<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit;

use Contenir\View\ConfigProvider;
use Contenir\View\Module;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Module::class)]
#[Group('unit')]
final class ModuleTest extends TestCase
{
    #[Test]
    public function exposesTheConfigProviderViewHelpers(): void
    {
        static::assertSame(
            (new ConfigProvider())->getViewHelperConfig(),
            (new Module())->getConfig()['view_helpers'],
        );
    }

    #[Test]
    public function setsTheSharedViewManagerDefaults(): void
    {
        $viewManager = (new Module())->getConfig()['view_manager'];

        static::assertSame(
            [
                'display_not_found_reason' => true,
                'display_exceptions'       => true,
                'doctype'                  => 'HTML5',
                'not_found_template'       => 'error/404',
                'forbidden_template'       => 'error/403',
                'exception_template'       => 'error/index',
                'strategies'               => ['ViewJsonStrategy'],
            ],
            $viewManager,
        );
    }
}
