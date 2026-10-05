<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit\Helper;

use Contenir\View\Helper\Icon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Icon::class)]
#[Group('unit')]
final class IconTest extends TestCase
{
    #[Test]
    public function exposesItsDefaults(): void
    {
        $helper = new Icon();

        static::assertSame(['i', null, './public/asset/icon', 'svg'], [
            $helper->getTag(),
            $helper->getClass(),
            $helper->getBasePath(),
            $helper->getExtension(),
        ]);
    }

    #[Test]
    public function settersChangeTheDefaults(): void
    {
        $helper = (new Icon())->setTag('span')
            ->setClass('icon')
            ->setBasePath('/icons')
            ->setExtension('png');

        static::assertSame(['span', 'icon', '/icons', 'png'], [
            $helper->getTag(),
            $helper->getClass(),
            $helper->getBasePath(),
            $helper->getExtension(),
        ]);
    }
}
