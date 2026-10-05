<?php

declare(strict_types=1);

namespace Contenir\View\Helper\Factory;

use Contenir\View\Helper\Acl as AclHelper;
use Laminas\Permissions\Acl\AclInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function get_debug_type;
use function sprintf;

/**
 * Builds the acl() helper from the application's "Application\Acl\Acl"
 * service.
 *
 * @api
 */
final class AclFactory
{
    /**
     * The service the application registers its ACL under. A string, as
     * the class lives in the application, not in this package.
     *
     * @mago-expect lint:no-literal-namespace-string The class is the application's, not loadable here.
     */
    public const string SERVICE = 'Application\Acl\Acl';

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ServiceNotCreatedException when the service is not an ACL.
     *
     * @mago-expect analysis:mixed-assignment Container services are untyped; the type is checked here.
     */
    public function __invoke(ContainerInterface $container): AclHelper
    {
        $acl = $container->get(self::SERVICE);
        if (! $acl instanceof AclInterface) {
            throw new ServiceNotCreatedException(sprintf(
                'Service "%s" must implement %s, %s given',
                self::SERVICE,
                AclInterface::class,
                get_debug_type($acl),
            ));
        }

        return new AclHelper($acl);
    }
}
