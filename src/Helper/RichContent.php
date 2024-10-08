<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class RichContent extends AbstractHelper
{
    protected $template = [
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

    public function __invoke($content, array $template = [])
    {
        $template = array_merge($this->template, $template);
        $html     = '';

        if ($content !== null) {
            $sections = preg_split('/<(\w+)([^>]*)>([^<]*)__SECTION__([^<]*)<\/\1>/msi',
                (string)$content,
                -1,
                PREG_SPLIT_NO_EMPTY);
            foreach ($sections as $section) {
                $html .= $this->formatSection($section, $template);
            }
        }

        return $html;
    }

    protected function formatSection($section, $template)
    {
        extract($template);

        $html      = '';
        $columns   = preg_split('/<(\w+)([^>]*)>([^<]*)__COL__([^<]*)<\/\1>/msi',
            (string)$section,
            -1,
            PREG_SPLIT_NO_EMPTY);
        $columnCnt = count($columns);

        if ($columnCnt > 1) {
            $columnClass = $columnClass[$columnCnt] ?? $columnClass['default'] ?? null;

            foreach ($columns as $column) {
                $tagClass = ($columnClass) ? sprintf(' class="%s"', $this->view->EscapeHtml($columnClass)) : '';
                $tagStart = ($columnTag) ? sprintf('<%s%s>', $columnTag, $tagClass) : '';
                $tagEnd   = ($columnTag) ? sprintf('</%s>', $columnTag) : '';
                $html     .= sprintf('%s%s%s', $tagStart, trim($column), $tagEnd);
            }
            $tagClass = ($innerClass) ? sprintf(' class="%s"', $this->view->EscapeHtml($innerClass)) : '';
            $tagStart = ($innerTag) ? sprintf('<%s%s>', $innerTag, $tagClass) : '';
            $tagEnd   = ($innerTag) ? sprintf('</%s>', $innerTag) : '';
            $html     = sprintf('%s%s%s', $tagStart, trim($html), $tagEnd);

            $tagClass = ($outerClass) ? sprintf(' class="%s"', $this->view->EscapeHtml($outerClass)) : '';
            $tagStart = ($outerTag) ? sprintf('<%s%s>', $outerTag, $tagClass) : '';
            $tagEnd   = ($outerTag) ? sprintf('</%s>', $outerTag) : '';
            $html     = sprintf('%s%s%s', $tagStart, trim($html), $tagEnd);
        } else {
            $html     .= join($columns);
        }

        return $html;
    }
}
