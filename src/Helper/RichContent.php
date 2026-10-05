<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Stringable;

use function array_filter;
use function array_map;
use function array_values;
use function count;
use function implode;
use function is_string;
use function preg_split;
use function sprintf;
use function trim;

use const PREG_SPLIT_NO_EMPTY;

/**
 * Lays out rich-text content in grid sections. A paragraph-like element
 * containing only "__SECTION__" starts a new section, and one containing
 * "__COL__" starts a new column. Sections with more than one column are
 * wrapped in the template's outer, inner and column elements; single-column
 * sections are output as they are.
 *
 * @api
 *
 * @psalm-type Template = array{
 *     outerTag: string,
 *     outerClass: string,
 *     innerTag: string,
 *     innerClass: string,
 *     columnTag: string,
 *     columnClass: array<array-key, string>,
 * }
 */
class RichContent extends AbstractHelper
{
    use PHPViewTrait;

    private const string SECTION_PATTERN = '/<(\w+)([^>]*)>([^<]*)__SECTION__([^<]*)<\/\1>/mi';
    private const string COLUMN_PATTERN  = '/<(\w+)([^>]*)>([^<]*)__COL__([^<]*)<\/\1>/mi';

    /** @var Template */
    protected array $template = [
        'outerTag'    => 'section',
        'outerClass'  => 'grid grid--content',
        'innerTag'    => '',
        'innerClass'  => '',
        'columnTag'   => 'div',
        'columnClass' => [
            'default' => 'grid__col',
            2         => 'grid__col grid__col--2',
            3         => 'grid__col grid__col--3',
            4         => 'grid__col grid__col--4',
        ],
    ];

    /**
     * Splits on a marker element. A failed split (a PCRE error) yields no
     * parts rather than a false entry.
     *
     * @return list<string>
     */
    private static function split(string $pattern, string $subject): array
    {
        $parts = preg_split($pattern, $subject, limit: -1, flags: PREG_SPLIT_NO_EMPTY);

        return array_values(array_filter((array) $parts, is_string(...)));
    }

    /**
     * @param Template $template
     */
    protected function formatSection(string $section, array $template): string
    {
        $columns     = self::split(self::COLUMN_PATTERN, $section);
        $columnCount = count($columns);

        if ($columnCount <= 1) {
            return implode('', $columns);
        }

        $columnClass = $template['columnClass'][$columnCount] ?? $template['columnClass']['default'] ?? '';

        $html = implode('', array_map(
            fn(string $column): string => $this->formatWrapper($column, $columnClass, $template['columnTag']),
            $columns,
        ));

        $html = $this->formatWrapper($html, $template['innerClass'], $template['innerTag']);

        return $this->formatWrapper($html, $template['outerClass'], $template['outerTag']);
    }

    protected function formatWrapper(string $html, string $class, string $tag): string
    {
        if ('' === $tag) {
            return trim($html);
        }

        $tagClass = '' === $class ? '' : sprintf(' class="%s"', $this->escapeHtml($class));

        return sprintf('<%1$s%2$s>%3$s</%1$s>', $tag, $tagClass, trim($html));
    }

    /**
     * @param array{
     *     outerTag?: string,
     *     outerClass?: string,
     *     innerTag?: string,
     *     innerClass?: string,
     *     columnTag?: string,
     *     columnClass?: array<array-key, string>,
     * } $template Overrides for this call. "columnClass" is keyed by column count, with a "default".
     */
    public function __invoke(string|Stringable|null $content, array $template = []): string
    {
        if (null === $content) {
            return '';
        }

        $template = [...$this->template, ...$template];

        return implode('', array_map(
            fn(string $section): string => $this->formatSection($section, $template),
            self::split(self::SECTION_PATTERN, (string) $content),
        ));
    }
}
