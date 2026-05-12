<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHtmlElement;
use Laminas\View\Helper\Placeholder\Container\AbstractContainer;

class Video extends AbstractHtmlElement
{
    use PHPViewTrait;

    protected array $options = [
        'videoClass'        => 'video',
        'videoWrapperClass' => '',
        /**
         * Explicit poster URL. When set, becomes the LCP candidate so the
         * fullscreen-hero LCP isn't blocked on the first video frame.
         */
        'poster'            => null,
        /**
         * When a poster is set and this is true, the helper queues a
         * <link rel="preload" as="image" fetchpriority="high"> via HeadLink
         * so the poster lands in the request waterfall ahead of late-
         * discovered resources.
         */
        'preloadPoster'     => true,
        /**
         * <video preload="..."> attribute. Defaults to `none` so the video
         * file doesn't compete with critical CSS/JS during initial paint;
         * the Videoplay component (or any IntersectionObserver-driven
         * autoplay) triggers the actual fetch when the element enters view.
         * Pass `metadata` if you want browsers to fetch the moov box ahead
         * of play, or `auto` to restore the old behaviour.
         */
        'preload'           => 'none',
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
                    return $this->renderVideoElement($view, $videoClass, $path, $controls, $options);
                }

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
                break;

            case 'youtube':
                $template = <<<ENDHTML
<iframe class="%s" src="https://www.youtube.com/embed/%s?fs=1&amp;showinfo=0"></iframe>
ENDHTML;
                break;

            default:
                return $this->renderVideoElement($view, $videoClass, $path, $controls, $options);
        }

        return sprintf(
            $template,
            $view->EscapeHtmlAttr($videoClass),
            $view->EscapeHtmlAttr($mediaInfo['path'])
        );
    }

    /**
     * Render a `<video>` element. Centralised so both the raw-MP4 default
     * and the Vimeo "external" branch share attribute handling (poster,
     * preload, autoplay flags) and the same HeadLink preload injection.
     */
    protected function renderVideoElement(
        $view,
        string $videoClass,
        string $src,
        bool $controls,
        array $options
    ): string {
        $attributes = [
            sprintf('class="%s"', $view->EscapeHtmlAttr($videoClass)),
            'playsinline',
        ];

        if (! empty($options['poster'])) {
            $attributes[] = sprintf('poster="%s"', $view->EscapeHtmlAttr($options['poster']));
        }

        if (! empty($options['preload'])) {
            $attributes[] = sprintf('preload="%s"', $view->EscapeHtmlAttr($options['preload']));
        }

        if ($controls) {
            $attributes[] = 'controls';
        } else {
            $attributes[] = 'muted';
            $attributes[] = 'autoplay';
            $attributes[] = 'loop';
        }

        if (! empty($options['poster']) && ! empty($options['preloadPoster'])) {
            $this->injectPosterPreload($view, $options['poster']);
        }

        return sprintf(
            '<video %s><source src="%s"></video>',
            implode(' ', $attributes),
            $view->EscapeHtmlAttr($src)
        );
    }

    /**
     * Queue a high-priority preload <link> for the poster image so it
     * arrives ahead of the late-discovered video element. Dedups against
     * existing entries because a page can legitimately render the same
     * section partial twice (e.g. preview + live) and we only want one
     * preload per URL.
     */
    protected function injectPosterPreload($view, string $poster): void
    {
        $headLink = $view->plugin('headLink');

        foreach ($headLink->getContainer() as $existing) {
            if (
                ($existing->rel ?? null) === 'preload'
                && ($existing->href ?? null) === $poster
            ) {
                return;
            }
        }

        $headLink([
            'rel'           => 'preload',
            'href'          => $poster,
            'as'            => 'image',
            'fetchpriority' => 'high',
        ], AbstractContainer::PREPEND);
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
