<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class RichContent extends AbstractHelper
{
    use PHPViewTrait;

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
            4         => 'grid__col grid__col--4'
        ]
    ];

    public function __invoke($content, array $template = []): string
    {
        $template = array_merge($this->template, $template);
        $html     = '';

        if ($content !== null) {
            $sections = preg_split('/<(\w+)([^>]*)>([^<]*)__SECTION__([^<]*)<\/\1>/mi',
                (string)$content,
                -1,
                PREG_SPLIT_NO_EMPTY);
            foreach ($sections as $section) {
                $html .= $this->formatSection($section, $template);
            }
        }

        return $html;
    }

    protected function formatSection($section, $template): string
    {
        extract($template);
        $view    = $this->getPHPView();

        $html      = '';
        $columns   = preg_split('/<(\w+)([^>]*)>([^<]*)__COL__([^<]*)<\/\1>/mi',
            (string)$section,
            -1,
            PREG_SPLIT_NO_EMPTY);
        $columnCnt = count($columns);

        if ($columnCnt > 1) {
            $columnClass = $columnClass[$columnCnt] ?? $columnClass['default'] ?? null;

            foreach ($columns as $column) {
                $tagClass = ($columnClass) ? sprintf(' class="%s"', $view->EscapeHtml($columnClass)) : '';
                $tagStart = ($columnTag) ? sprintf('<%s%s>', $columnTag, $tagClass) : '';
                $tagEnd   = ($columnTag) ? sprintf('</%s>', $columnTag) : '';
                $html     .= sprintf('%s%s%s', $tagStart, trim($column), $tagEnd);
            }

            $html = $this->formatWrapper($html, $innerClass, $innerTag);
            $html = $this->formatWrapper($html, $outerClass, $outerTag);
        } else {
            $html .= join($columns);
        }

        return $html;
    }

    protected function formatWrapper($html, $class, $tag): string
    {
        $view    = $this->getPHPView();

        $tagClass = ($class) ? sprintf(' class="%s"', $view->EscapeHtml($class)) : '';
        $tagStart = ($tag) ? sprintf('<%s%s>', $tag, $tagClass) : '';
        $tagEnd   = ($tag) ? sprintf('</%s>', $tag) : '';

        return sprintf('%s%s%s', $tagStart, trim($html), $tagEnd);
    }
}
