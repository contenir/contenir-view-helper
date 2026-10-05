<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Integration\Helper;

use Contenir\View\Helper\ResourceLink;
use Contenir\View\Tests\Trait\PhpRendererTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function array_map;
use function json_decode;

#[CoversClass(ResourceLink::class)]
#[Group('integration')]
#[Group('view')]
final class ResourceLinkTest extends TestCase
{
    use PhpRendererTrait;

    private const string JSON = '[
        {"fields": {"url": "/contact", "cta": "Get in touch", "target": "_blank"}},
        {"fields": {"url": "example.org", "cta": "", "target": ""}},
        {"fields": {"url": "/plain"}},
        {"fields": {"url": ""}},
        {"fields": {"cta": "No URL"}},
        {"no_fields": true},
        "not an item"
    ]';

    /**
     * @return array<string, array{string|array<array-key, mixed>|null, list<array{url: string, cta: string, target: string|null}>}>
     */
    public static function valueProvider(): array
    {
        $parsed = [
            ['url' => 'https://www.example.com/contact', 'cta' => 'Get in touch', 'target' => '_blank'],
            ['url' => 'http://example.org', 'cta' => 'Find out more', 'target' => null],
            ['url' => 'https://www.example.com/plain', 'cta' => 'Find out more', 'target' => null],
        ];

        return [
            'no value'             => [null, []],
            'empty string'         => ['', []],
            'empty list'           => [[], []],
            'plain URL'            => [
                'https://www.example.org/news',
                [['url' => 'https://www.example.org/news', 'cta' => 'Find out more', 'target' => null]],
            ],
            'JSON object is a URL' => [
                '{"url": "/x"}',
                [['url' => '', 'cta' => 'Find out more', 'target' => null]],
            ],
            'JSON list'            => [self::JSON, $parsed],
            'decoded list'         => [(array) json_decode(self::JSON, associative: false), $parsed],
        ];
    }

    #[Test]
    public function aDefaultCallToActionAppliesToThisAndLaterCalls(): void
    {
        $helper = $this->createRenderer()->plugin(ResourceLink::class);

        static::assertSame(
            ['Read more', 'Read more'],
            [$helper('/a', defaultCta: 'Read more')[0]->cta, $helper('/b')[0]->cta],
        );
    }

    /**
     * @param string|array<array-key, mixed>|null                              $value
     * @param list<array{url: string, cta: string, target: string|null}> $expected
     */
    #[Test]
    #[DataProvider('valueProvider')]
    public function normalisesTheValueIntoLinks(string|array|null $value, array $expected): void
    {
        $links = $this->createRenderer()->plugin(ResourceLink::class)($value);

        static::assertSame($expected, array_map(static fn(object $link): array => (array) $link, $links));
    }
}
