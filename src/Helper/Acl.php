<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\Permissions\Acl\AclInterface;
use Laminas\View\Helper\AbstractHelper;

/**
 * Exposes the application ACL to view scripts: `$this->acl()->isAllowed(...)`.
 *
 * @api
 */
final class Acl extends AbstractHelper
{
    public function __construct(
        private AclInterface $acl,
    ) {}

    public function __invoke(): AclInterface
    {
        return $this->acl;
    }
}
