<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\AbstractHtmlElement;
use Laminas\View\Helper\HeadLink;
use Laminas\View\Helper\Placeholder\Container\AbstractContainer;
use Laminas\View\Renderer\PhpRenderer;

use function array_filter;
use function implode;
use function preg_match;
use function sprintf;
use function str_contains;

/**
 * Renders a video: a Vimeo or YouTube embed for a provider URL, or a
 * <video> element for anything else (including Vimeo "external" file
 * links).
 *
 * @api
 */
class Video extends AbstractHtmlElement
{
    use PHPViewTrait;

    private const string VIMEO_TEMPLATE = <<<'HTML'
        <iframe
            class="%s"
            src="https://player.vimeo.com/video/%s?autoplay=0&mute=0&loop=0&title=0&byline=0&portrait=0"
            data-vimeo-portrait="false"
            data-vimeo-byline="false"
            data-vimeo-title="false"
            data-vimeo="1"
            allowfullscreen>
        </iframe>
        HTML;

    private const string YOUTUBE_TEMPLATE = '<iframe class="%s" src="https://www.youtube.com/embed/%s?fs=1&amp;showinfo=0"></iframe>';

    /**
     * @var array{
     *     videoClass: string,
     *     videoWrapperClass: string,
     *     poster: string|null,
     *     preloadPoster: bool,
     *     preload: string,
     * }
     */
    protected array $options = [
        'videoClass'        => 'video',
        'videoWrapperClass' => '',
        /**
         * Explicit poster URL. When set, becomes the LCP candidate so the
         * fullscreen-hero LCP isn't blocked on the first video frame.
         */
        'poster' => null,
        /**
         * When a poster is set and this is true, the helper queues a
         * <link rel="preload" as="image" fetchpriority="high"> via HeadLink
         * so the poster lands in the request waterfall ahead of late-
         * discovered resources.
         */
        'preloadPoster' => true,
        /**
         * <video preload="..."> attribute. Defaults to `metadata` so the
         * browser fetches just the moov box (a few KB) — enough to satisfy
         * autoplay policy in Safari/Chrome while keeping the video bytes
         * themselves off the critical path until playback starts. Pass
         * `none` to block all preloading (only viable when JS reliably
         * calls .load() + .play() on viewport entry — `preload="none"`
         * + autoplay is the *least* likely combo to autoplay because the
         * browser interprets it as "developer doesn't want this loaded
         * yet"), or `auto` for the legacy eager-fetch behaviour.
         */
        'preload' => 'metadata',
    ];

    /**
     * Queue a high-priority preload <link> for the poster image so it
     * arrives ahead of the late-discovered video element. Dedups against
     * existing entries because a page can legitimately render the same
     * section partial twice (e.g. preview + live) and we only want one
     * preload per URL.
     */
    protected function injectPosterPreload(PhpRenderer $view, string $poster): void
    {
        $headLink = $view->plugin(HeadLink::class);

        foreach ($headLink->getContainer() as $existing) {
            if (($existing->rel ?? null) === 'preload' && ($existing->href ?? null) === $poster) {
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

    /**
     * @return array{provider: string|null, path: string}
     *
     * @mago-expect analysis:possibly-undefined-int-array-index A successful match always fills its groups.
     * @mago-expect analysis:invalid-return-statement A successful match always fills its groups.
     */
    protected function parsePath(string $path): array
    {
        $match = [];
        if (
            1 === preg_match(
                '/(http:\/\/)?(?:www.)?(vimeo|youtube).com\/(?:watch\?v=|video\/)?(.*?)(?:\z|&)/',
                $path,
                $match,
            )
        ) {
            return ['provider' => $match[2], 'path' => $match[3]];
        }

        if (1 === preg_match('/^(\w+):(.*)$/', $path, $match)) {
            return ['provider' => $match[1], 'path' => $match[2]];
        }

        return ['provider' => null, 'path' => ''];
    }

    /**
     * Render a `<video>` element. Centralised so both the raw-MP4 default
     * and the Vimeo "external" branch share attribute handling (poster,
     * preload, autoplay flags) and the same HeadLink preload injection.
     *
     * @param array{poster: string|null, preloadPoster: bool, preload: string, ...} $options
     */
    protected function renderVideoElement(
        PhpRenderer $view,
        string $videoClass,
        string $src,
        bool $controls,
        array $options,
    ): string {
        $poster     = (string) $options['poster'];
        $attributes = [
            sprintf('class="%s"', $this->escapeHtmlAttr($videoClass)),
            'playsinline',
            '' === $poster ? null : sprintf('poster="%s"', $this->escapeHtmlAttr($poster)),
            '' === $options['preload'] ? null : sprintf('preload="%s"', $this->escapeHtmlAttr($options['preload'])),
            ...($controls ? ['controls'] : ['muted', 'autoplay', 'loop']),
        ];

        if ('' !== $poster && $options['preloadPoster']) {
            $this->injectPosterPreload($view, $poster);
        }

        return sprintf(
            '<video %s><source src="%s"></video>',
            implode(' ', array_filter($attributes, static fn(?string $attribute): bool => null !== $attribute)),
            $this->escapeHtmlAttr($src),
        );
    }

    /**
     * @param array{
     *     videoClass?: string,
     *     videoWrapperClass?: string,
     *     poster?: string|null,
     *     preloadPoster?: bool,
     *     preload?: string,
     * } $options
     * @param bool $controls Show the native controls; without them the video autoplays muted on loop.
     *
     * @throws RuntimeException when the helper is not attached to a PhpRenderer.
     */
    public function __invoke(string $path, array $options = [], bool $controls = false): string
    {
        $options = [...$this->options, ...$options];
        $view    = $this->getPHPView();
        $media   = $this->parsePath($path);

        $template = match ($media['provider']) {
            'vimeo'   => str_contains($path, 'external') ? null : self::VIMEO_TEMPLATE,
            'youtube' => self::YOUTUBE_TEMPLATE,
            default   => null,
        };

        if (null === $template) {
            return $this->renderVideoElement($view, $options['videoClass'], $path, $controls, $options);
        }

        $embed = sprintf(
            $template,
            $this->escapeHtmlAttr($options['videoClass']),
            $this->escapeHtmlAttr($media['path']),
        );

        if ('vimeo' !== $media['provider'] || '' === $options['videoWrapperClass']) {
            return $embed;
        }

        return sprintf('<div class="%s">%s</div>', $this->escapeHtml($options['videoWrapperClass']), $embed);
    }
}
