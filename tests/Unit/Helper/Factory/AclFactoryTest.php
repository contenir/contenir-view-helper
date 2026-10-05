<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit\Helper\Factory;

use Contenir\View\Helper\Factory\AclFactory;
use Contenir\View\Tests\TestAsset\Container\InMemoryContainer;
use Laminas\Permissions\Acl\AclInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(AclFactory::class)]
#[Group('unit')]
final class AclFactoryTest extends TestCase
{
    #[Test]
    public function buildsTheHelperOnTheApplicationAcl(): void
    {
        $acl = $this->createStub(AclInterface::class);

        static::assertSame($acl, (new AclFactory())(new InMemoryContainer([AclFactory::SERVICE => $acl]))());
    }

    #[Test]
    public function rejectsAServiceThatIsNotAnAcl(): void
    {
        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionMessage(
            'Service "Application\Acl\Acl" must implement Laminas\Permissions\Acl\AclInterface, stdClass given',
        );

        (new AclFactory())(new InMemoryContainer([AclFactory::SERVICE => new stdClass()]));
    }
}
