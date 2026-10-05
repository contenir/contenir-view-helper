<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Unit\Helper;

use Contenir\View\Helper\FileType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FileType::class)]
#[Group('unit')]
final class FileTypeTest extends TestCase
{
    /**
     * @return array<string, array{string|null, string}>
     */
    public static function mimeTypeProvider(): array
    {
        return [
            'pdf'     => ['application/pdf', 'PDF'],
            'word'    => ['application/msword', 'Document'],
            'unknown' => [null, 'Document'],
        ];
    }

    #[Test]
    #[DataProvider('mimeTypeProvider')]
    public function labelsTheMimeType(?string $mimeType, string $expected): void
    {
        static::assertSame($expected, (new FileType())($mimeType));
    }
}
