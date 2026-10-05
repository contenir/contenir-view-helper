<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit\Helper;

use Contenir\View\Helper\Acl;
use Laminas\Permissions\Acl\AclInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Acl::class)]
#[Group('unit')]
final class AclTest extends TestCase
{
    #[Test]
    public function returnsTheInjectedAcl(): void
    {
        $acl = $this->createStub(AclInterface::class);

        static::assertSame($acl, (new Acl($acl))());
    }
}
