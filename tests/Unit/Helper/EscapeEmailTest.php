<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit\Helper;

use Contenir\View\Helper\EscapeEmail;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(EscapeEmail::class)]
#[Group('unit')]
final class EscapeEmailTest extends TestCase
{
    #[Test]
    public function encodesEachCharacterAsAHexEntityForDisplay(): void
    {
        static::assertSame('<!-- . -->&#x61;<!-- . -->&#x40;<!-- . -->&#x62;', (new EscapeEmail())('a@b'));
    }

    #[Test]
    public function percentEncodesBehindAnEntityEncodedMailtoForLinks(): void
    {
        static::assertSame(
            '&#109;&#97;&#105;&#108;&#116;&#111;&#58;%61%40%62',
            (new EscapeEmail())('a@b', mailto: true),
        );
    }

    #[Test]
    public function returnsAnEmptyStringForAnEmptyAddress(): void
    {
        static::assertSame('', (new EscapeEmail())(''));
    }
}
