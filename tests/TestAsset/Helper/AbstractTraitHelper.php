<?php

declare(strict_types=1);

namespace Contenir\View\Tests\TestAsset\Helper;

use Contenir\View\Helper\PHPViewTrait;
use Laminas\View\Helper\AbstractHelper;

/**
 * A consumer's base helper that uses PHPViewTrait, so subclasses reach the
 * trait's methods through inheritance.
 */
abstract class AbstractTraitHelper extends AbstractHelper
{
    use PHPViewTrait;
}
