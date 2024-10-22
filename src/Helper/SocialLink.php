<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class SocialLink extends AbstractHelper
{
    use PHPViewTrait;

    protected array $socialList = [
        'instagram' => [
            'title' => 'Instagram',
            'mask'  => 'http(s)?://(www\.)?instagram.com(/)?',
            'url'   => 'https://instagram.com/'
        ],
        'behance' => [
            'title' => 'Behance',
            'mask'  => 'http(s)?://(www\.)?behance.net(/)?',
            'url'   => 'https://behance.net/'
        ],
        'linkedin' => [
            'title' => 'LinkedIn',
            'mask'  => 'http(s)?://(www\.)?linkedin.com/in(/)?',
            'url'   => 'https://linkedin.com/in/'
        ],
        'facebook' => [
            'title' => 'Facebook',
            'mask'  => 'http(s)?://(www\.)?(www.)?facebook.com(/)?',
            'url'   => 'https://facebook.com/'
        ],
        'issuu' => [
            'title' => 'Issuu',
            'mask'  => 'http(s)?://(www\.)?issuu.com(/)?',
            'url'   => 'https://issuu.com/'
        ],
        'medium' => [
            'title' => 'Medium',
            'mask'  => 'http(s)?://(www\.)?medium.com/@',
            'url'   => 'https://medium.com/@'
        ],
        'flickr' => [
            'title' => 'Flickr',
            'mask'  => 'https://www.',
            'url'   => 'https://www.'
        ],
        'vimeo' => [
            'title' => 'Vimeo',
            'mask'  => 'http(s)?://(www\.)?vimeo.com(/)?',
            'url'   => 'https://vimeo.com/'
        ],
        'youtube' => [
            'title' => 'Youtube',
            'mask'  => 'http(s)?://(www\.)?youtube.com/channel(/)?',
            'url'   => 'https://youtube.com/channel/'
        ]
    ];

    protected array $options = [
        'link_class' => 'navbar__link',
        'icon_class' => 'navbar__icon',
        'icon'       => false,
    ];

    public function __invoke($socialId, $link, array $options = []): string
    {
        $options = array_merge($this->options, $options);
        $view    = $this->getPHPView();

        if (str_starts_with($socialId, 'social_')) {
            $socialId = substr($socialId, 7);
        }

        $social = $this->socialList[$socialId] ?? null;
        if (! $social) {
            return '';
        }

        if ($options['icon']) {
            $content = file_get_contents("./public/asset/icon/icon-$socialId.svg");
        } else {
            $content = $social['title'];
        }
        $mask    = $social['mask'];
        $baseUrl = $social['url'];

        $url = $view->UrlFormat(sprintf('%s%s', $baseUrl, preg_replace('|' . $mask . '|', '', (string)$link)));

        return sprintf('<a aria-label="%s" class="%s" target="_blank" href="%s">%s</a>', $social['title'], $options['link_class'], $url, $content);
    }
}
