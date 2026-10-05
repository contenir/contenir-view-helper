<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\Uri\Exception\InvalidArgumentException;
use Laminas\Uri\UriFactory;
use Laminas\Uri\UriInterface;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\ServerUrl;

use function preg_match;
use function preg_replace_callback;
use function str_starts_with;

/**
 * Normalises a URL and renders it through a format of %part% placeholders:
 * %scheme%, %userinfo%, %host%, %port%, %path%, %query% and %fragment%.
 * Root-relative URLs are made absolute against the current server, and
 * URLs without a scheme are assumed to be http.
 *
 * @api
 */
class UrlFormat extends AbstractHelper
{
    use PHPViewTrait;

    protected string $format = '%scheme%%host%%path%';

    private static function affix(string $value, string $prefix = '', string $suffix = ''): string
    {
        return '' === $value ? '' : $prefix . $value . $suffix;
    }

    private static function formatPart(UriInterface $uri, string $part): string
    {
        return match ($part) {
            'scheme'   => self::affix((string) $uri->getScheme(), suffix: '://'),
            'userinfo' => (string) $uri->getUserInfo(),
            'host'     => (string) $uri->getHost(),
            'port'     => (string) $uri->getPort(),
            'path'     => (string) $uri->getPath(),
            'query'    => self::affix((string) $uri->getQuery(), prefix: '?'),
            'fragment' => self::affix((string) $uri->getFragment(), prefix: '#'),
            default    => '',
        };
    }

    /**
     * @return string The formatted URL, or an empty string when $url is empty or invalid.
     *
     * @throws RuntimeException when the helper is not attached to a PhpRenderer.
     */
    public function __invoke(?string $url = null, ?string $format = null): string
    {
        if (null === $url || '' === $url) {
            return '';
        }

        if (1 !== preg_match('/^[a-z]+:\/\//', $url)) {
            $url = str_starts_with($url, '/')
                ? $this->getPHPView()->plugin(ServerUrl::class)->__invoke() . $url
                : "http://{$url}";
        }

        try {
            $uri = UriFactory::factory($url);
        } catch (InvalidArgumentException) {
            return '';
        }

        return (string) preg_replace_callback(
            '/%(\w+)%/',
            /** @param array<array-key, string> $matches */
            static fn(array $matches): string => self::formatPart($uri, $matches[1] ?? ''),
            $format ?? $this->format,
        );
    }
}
