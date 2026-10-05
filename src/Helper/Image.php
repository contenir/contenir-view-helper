<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHtmlElement;

use function http_build_query;
use function is_string;
use function parse_url;
use function preg_match;
use function sprintf;

use const PHP_URL_PATH;

/**
 * Renders a lazy-loaded <picture>/<img> pair. Local paths are served from
 * the configured CDN host with a 20px placeholder in "src" and the full
 * image in "data-src"; absolute http(s) URLs are used as they are.
 *
 * @api
 */
class Image extends AbstractHtmlElement
{
    protected ?string $host;
    protected string $scheme;

    /**
     * @param array<array-key, mixed> $options CDN "host", and its scheme as "method" (default https).
     *
     * @mago-expect analysis:mixed-assignment Options are untyped configuration; narrowed here.
     */
    public function __construct(array $options = [])
    {
        $host         = $options['host'] ?? null;
        $scheme       = $options['method'] ?? null;
        $this->host   = is_string($host) ? $host : null;
        $this->scheme = is_string($scheme) ? $scheme : 'https';
    }

    /**
     * @param array<string, scalar|null> $attributes        <img> attributes. "resizeWidth" is
     *                                                      consumed as the CDN width parameter.
     * @param array<string, scalar|null> $pictureAttributes <picture> attributes.
     */
    public function __invoke(string $path, array $attributes = [], array $pictureAttributes = []): string
    {
        $src     = $path;
        $dataSrc = $path;

        if (1 !== preg_match('/^http(s)?:/', $path)) {
            $urlPath  = parse_url($path, component: PHP_URL_PATH);
            $fullPath = (null === $this->host || '' === $this->host ? '' : "{$this->scheme}://{$this->host}")
            . (is_string($urlPath) ? $urlPath : '');

            $resizeParams = ['auto_optimize' => 'high'];

            $resizeWidth = $attributes['resizeWidth'] ?? null;
            if (null !== $resizeWidth) {
                $resizeParams['width'] = $resizeWidth;
                unset($attributes['resizeWidth']);
            }

            $src     = "{$fullPath}?width=20&auto_optimize=high";
            $dataSrc = $fullPath . '?' . http_build_query($resizeParams);
        }

        $imgAttributes = [
            'src'      => $src,
            'data-src' => $dataSrc,
            'class'    => 'img',
            'alt'      => '',
            ...$attributes,
        ];

        $pictureAttributes = ['class' => 'picture', ...$pictureAttributes];

        return sprintf(
            '<picture%s><img%s data-lazyload%s</picture>',
            $this->htmlAttribs($pictureAttributes),
            $this->htmlAttribs($imgAttributes),
            $this->getClosingBracket(),
        );
    }
}
