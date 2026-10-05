<?php

declare(strict_types=1);

namespace Contenir\View\Tests\TestAsset\Form;

use Laminas\Form\Element;

/**
 * An element whose short name is "Radio" but which is not a laminas-form
 * MultiCheckbox, so it has no value options.
 */
final class Radio extends Element {}
