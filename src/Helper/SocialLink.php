<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\AbstractHelper;

use function file_get_contents;
use function is_file;
use function preg_replace;
use function sprintf;
use function str_starts_with;
use function substr;

/**
 * Renders a link to a social network profile from a handle or a full
 * profile URL, labelled with the network's name or its inline SVG icon.
 *
 * @api
 */
class SocialLink extends AbstractHelper
{
    use PHPViewTrait;

    /** @var array<string, array{title: string, mask: string, url: string}> */
    protected array $socialList = [
        'instagram' => [
            'title' => 'Instagram',
            'mask'  => 'http(s)?://(www\.)?instagram.com(/)?',
            'url'   => 'https://instagram.com/',
        ],
        'behance'   => [
            'title' => 'Behance',
            'mask'  => 'http(s)?://(www\.)?behance.net(/)?',
            'url'   => 'https://behance.net/',
        ],
        'linkedin'  => [
            'title' => 'LinkedIn',
            'mask'  => 'http(s)?://(www\.)?linkedin.com/in(/)?',
            'url'   => 'https://linkedin.com/in/',
        ],
        'facebook'  => [
            'title' => 'Facebook',
            'mask'  => 'http(s)?://(www\.)?(www.)?facebook.com(/)?',
            'url'   => 'https://facebook.com/',
        ],
        'issuu'     => [
            'title' => 'Issuu',
            'mask'  => 'http(s)?://(www\.)?issuu.com(/)?',
            'url'   => 'https://issuu.com/',
        ],
        'medium'    => [
            'title' => 'Medium',
            'mask'  => 'http(s)?://(www\.)?medium.com/@',
            'url'   => 'https://medium.com/@',
        ],
        'flickr'    => [
            'title' => 'Flickr',
            'mask'  => 'https://www.',
            'url'   => 'https://www.',
        ],
        'vimeo'     => [
            'title' => 'Vimeo',
            'mask'  => 'http(s)?://(www\.)?vimeo.com(/)?',
            'url'   => 'https://vimeo.com/',
        ],
        'youtube'   => [
            'title' => 'Youtube',
            'mask'  => 'http(s)?://(www\.)?youtube.com/channel(/)?',
            'url'   => 'https://youtube.com/channel/',
        ],
    ];

    /** @var array{link_class: string, icon_class: string, icon: bool} */
    protected array $options = [
        'link_class' => 'navbar__link',
        'icon_class' => 'navbar__icon',
        'icon'       => false,
    ];

    private static function readIcon(string $path): string
    {
        return is_file($path) ? (string) file_get_contents($path) : '';
    }

    /**
     * @param string                                                  $socialId A key of $socialList, optionally
     *                                                                          prefixed "social_".
     * @param string|null                                             $link     A handle or a profile URL.
     * @param array{link_class?: string, icon_class?: string, icon?: bool} $options
     *
     * @return string The link, or an empty string for an unknown network.
     *
     * @throws RuntimeException when the helper is not attached to a PhpRenderer.
     */
    public function __invoke(string $socialId, ?string $link, array $options = []): string
    {
        $options = [...$this->options, ...$options];

        if (str_starts_with($socialId, 'social_')) {
            $socialId = substr($socialId, offset: 7);
        }

        $social = $this->socialList[$socialId] ?? null;
        if (null === $social) {
            return '';
        }

        $content = $options['icon'] ? self::readIcon("./public/asset/icon/icon-{$socialId}.svg") : $social['title'];
        $handle  = (string) preg_replace("|{$social['mask']}|", replacement: '', subject: (string) $link);
        $url     = $this->getPHPView()->plugin(UrlFormat::class)($social['url'] . $handle);

        return sprintf(
            '<a aria-label="%s" class="%s" target="_blank" href="%s">%s</a>',
            $social['title'],
            $options['link_class'],
            $this->escapeHtml($url),
            $content,
        );
    }
}
