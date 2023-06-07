<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHtmlElement;

class Video extends AbstractHtmlElement
{
    protected $options = [
        'videoClass'        => 'video',
        'videoWrapperClass' => ''
    ];

    public function __invoke($path, array $options = [])
    {
        $html = '';

        $options = array_merge($this->options, $options);

        $mediaInfo = $this->parsePath($path);
        $provider  = $mediaInfo['provider'];

        $videoClass = $options['videoClass'];

        switch ($provider) {
            case 'vimeo':
                if (strpos($path, 'external') !== false) {
                    $template = <<<ENDHTML
<video
    class="%s"
    playsinline
    muted
    autoplay
    loop>
    <source src="{$path}"></source>
</video>
ENDHTML;
                    if (! empty($options['videoWrapperClass'])) {
                        $html = sprintf(
                            '<div class="%">%s</div>',
                            $options['videoWrapperClass'],
                            $html
                        );
                    }

                    $html = sprintf(
                        $template,
                        $this->view->EscapeHtmlAttr($videoClass),
                        $this->view->EscapeHtmlAttr($mediaInfo['path'])
                    );
                } else {
                    $template = <<<ENDHTML
<iframe
    class="%s"
    src="https://player.vimeo.com/video/%s?autoplay=0&mute=0&loop=0&title=0&byline=0&portrait=0"
    data-vimeo-portrait="false"
    data-vimeo-byline="false"
    data-vimeo-title="false"
    data-vimeo="1"
    allowfullscreen
    frameborder="0">
</iframe>
ENDHTML;
                    if (! empty($options['videoWrapperClass'])) {
                        $template = sprintf(
                            '<div class="%s">%s</div>',
                            $options['videoWrapperClass'],
                            $template
                        );
                    }

                    $html = sprintf(
                        $template,
                        $this->view->EscapeHtmlAttr($videoClass),
                        $this->view->EscapeHtmlAttr($mediaInfo['path'])
                    );
                }
                break;

            case 'youtube':
                $template = <<<ENDHTML
<iframe
    class="%s"
    src="https://www.youtube.com/embed/%s?fs=1&amp;showinfo=0"
    frameborder="0">
</iframe>
ENDHTML;
                $html = sprintf(
                    $template,
                    $this->view->EscapeHtmlAttr($videoClass),
                    $this->view->EscapeHtmlAttr($mediaInfo['path'])
                );
                break;

            default:
                $template = <<<ENDHTML
<video
    class="%s"
    playsinline
    muted
    autoplay
    loop>
    <source src="%s"></source>
</video>
ENDHTML;
                $html = sprintf(
                    $template,
                    $this->view->EscapeHtmlAttr($videoClass),
                    $this->view->EscapeHtmlAttr($mediaInfo['path'])
                );
                break;
        }

        return $html;
    }

    protected function parsePath($path)
    {
        $media = [
            'provider' => null,
            'id'       => null
        ];

        if (
            preg_match(
                '/(http:\/\/)?(?:www.)?(vimeo|youtube).com\/(?:watch\?v=|video\/)?(.*?)(?:\z|&)/',
                $path,
                $match,
                PREG_OFFSET_CAPTURE
            )
        ) {
            $media['provider'] = $match[2][0];
            $media['path']     = $match[3][0];
            return $media;
        }

        if (preg_match('/^(\w+):(.*)$/', $path, $match, PREG_OFFSET_CAPTURE)) {
            $media['provider'] = $match[1][0];
            $media['path']     = $match[2][0];
            return $media;
        }

        return $media;
    }
}
