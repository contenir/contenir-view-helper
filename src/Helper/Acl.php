<?php

namespace Contenir\View\Helper;

use Laminas\Permissions\Acl\AclInterface;
use Laminas\View\Helper\AbstractHelper;

class Acl extends AbstractHelper
{
    protected AclInterface $acl;

    public function __construct(AclInterface $acl)
    {
        $this->acl = $acl;
    }

    public function __invoke(): AclInterface
    {
        return $this->acl;
    }
}
