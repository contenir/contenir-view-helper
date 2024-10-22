<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHtmlElement;

class Video extends AbstractHtmlElement
{
    use PHPViewTrait;

    protected array $options = [
        'videoClass'        => 'video',
        'videoWrapperClass' => ''
    ];

    public function __invoke($path, array $options = [], $controls = false): string
    {
        $options = array_merge($this->options, $options);
        $view = $this->getPHPView();

        $mediaInfo = $this->parsePath($path);
        $provider  = $mediaInfo['provider'];

        $videoClass = $options['videoClass'];

        switch ($provider) {
            case 'vimeo':
                if (str_contains($path, 'external')) {
                    $videoAttributes = ($controls) ? ' controls' : ' muted autoplay loop';
                    $template        = <<<ENDHTML
<video
    class="%s"
    playsinline
    $videoAttributes>
    <source src="$path">
</video>
ENDHTML;
                } else {
                    $template = <<<ENDHTML
<iframe
    class="%s"
    src="https://player.vimeo.com/video/%s?autoplay=0&mute=0&loop=0&title=0&byline=0&portrait=0"
    data-vimeo-portrait="false"
    data-vimeo-byline="false"
    data-vimeo-title="false"
    data-vimeo="1"
    allowfullscreen>
</iframe>
ENDHTML;
                    if (! empty($options['videoWrapperClass'])) {
                        $template = sprintf(
                            '<div class="%s">%s</div>',
                            $options['videoWrapperClass'],
                            $template
                        );
                    }

                }
                break;

            case 'youtube':
                $template = <<<ENDHTML
<iframe class="%s" src="https://www.youtube.com/embed/%s?fs=1&amp;showinfo=0"></iframe>
ENDHTML;
                break;

            default:
                $videoAttributes = ($controls) ? ' controls' : ' muted autoplay loop';
                $template        = <<<ENDHTML
<video class="%s" playsinline $videoAttributes><source src="%s"></video>
ENDHTML;
                break;
        }

        return sprintf(
            $template,
            $view->EscapeHtmlAttr($videoClass),
            $view->EscapeHtmlAttr($mediaInfo['path'])
        );
    }

    protected function parsePath($path): array
    {
        $media = [
            'provider' => null,
            'id'       => null
        ];

        if (
            preg_match(
                '/(http:\/\/)?(?:www.)?(vimeo|youtube).com\/(?:watch\?v=|video\/)?(.*?)(?:\z|&)/',
                (string)$path,
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
