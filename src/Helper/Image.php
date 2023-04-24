<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHtmlElement;

class Image extends AbstractHtmlElement
{
    protected $scheme;
    protected $host;

    public function __construct($options)
    {
        $this->host = $options['host'] ?? null;
        $this->scheme = $options['method'] ?? 'https';
    }

    public function __invoke($path, array $attributes = [], array $pictureAttributes = [])
    {
        if (! preg_match('/^http(s)?:/', $path)) {
            $info = parse_url($path);
            $scheme = ($this->host) ? $this->scheme . '://' : null;

            $fullPath = join('', [
                $scheme,
                $this->host,
                $info['path']
            ]);

            $resizeParams = [
                'auto_optimize' => 'high'
            ];

            if (isset($attributes['resizeWidth'])) {
                $resizeParams['width'] = $attributes['resizeWidth'];
                unset($attributes['resizeWidth']);
            }

            $src = $fullPath . '?width=20&amp;auto_optimize=high';
            $dataSrc = $fullPath . '?' . http_build_query($resizeParams);
        } else {
            $src = $path;
            $dataSrc = $path;
        }


        $imgAttributes = array_merge([
            'src' => $src,
            'data-src' => $dataSrc,
            'class' => 'img',
            'alt' => ''
        ], $attributes);

        $pictureAttributes = array_merge([
            'class' => 'picture'
        ], $pictureAttributes);

        $imgHtml = sprintf('<img%s data-lazyload%s', $this->htmlAttribs($imgAttributes), $this->getClosingBracket());
        $pictureHtml = sprintf('<picture %s>%s</picture>', $this->htmlAttribs($pictureAttributes), $imgHtml);

        return $pictureHtml;
    }
}
