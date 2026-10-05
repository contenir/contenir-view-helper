<?php

declare(strict_types=1);

namespace Contenir\View;

use Laminas\ServiceManager\Factory\InvokableFactory;

/**
 * Registers the view helpers with a laminas-view helper plugin manager,
 * for Mezzio-style config aggregation and the Laminas component installer.
 *
 * @api
 */
final readonly class ConfigProvider
{
    /**
     * @return array{aliases: array<string, class-string>, factories: array<class-string, class-string>}
     */
    public function getViewHelperConfig(): array
    {
        return [
            'aliases'   => [
                'acl'          => Helper\Acl::class,
                'Acl'          => Helper\Acl::class,
                'cache'        => Helper\Cache::class,
                'Cache'        => Helper\Cache::class,
                'dateFormat'   => Helper\DateFormat::class,
                'DateFormat'   => Helper\DateFormat::class,
                'escapeEmail'  => Helper\EscapeEmail::class,
                'EscapeEmail'  => Helper\EscapeEmail::class,
                'fileSize'     => Helper\FileSize::class,
                'FileSize'     => Helper\FileSize::class,
                'fileType'     => Helper\FileType::class,
                'FileType'     => Helper\FileType::class,
                'formGroup'    => Helper\FormGroup::class,
                'FormGroup'    => Helper\FormGroup::class,
                'icon'         => Helper\Icon::class,
                'Icon'         => Helper\Icon::class,
                'image'        => Helper\Image::class,
                'Image'        => Helper\Image::class,
                'resourceLink' => Helper\ResourceLink::class,
                'ResourceLink' => Helper\ResourceLink::class,
                'richContent'  => Helper\RichContent::class,
                'RichContent'  => Helper\RichContent::class,
                'settings'     => Helper\Settings::class,
                'Settings'     => Helper\Settings::class,
                'socialLink'   => Helper\SocialLink::class,
                'SocialLink'   => Helper\SocialLink::class,
                'srcset'       => Helper\Srcset::class,
                'Srcset'       => Helper\Srcset::class,
                'truncate'     => Helper\Truncate::class,
                'Truncate'     => Helper\Truncate::class,
                'urlFormat'    => Helper\UrlFormat::class,
                'UrlFormat'    => Helper\UrlFormat::class,
                'video'        => Helper\Video::class,
                'Video'        => Helper\Video::class,
            ],
            'factories' => [
                Helper\Acl::class          => Helper\Factory\AclFactory::class,
                Helper\Cache::class        => Helper\Factory\CacheFactory::class,
                Helper\DateFormat::class   => InvokableFactory::class,
                Helper\EscapeEmail::class  => InvokableFactory::class,
                Helper\FileSize::class     => InvokableFactory::class,
                Helper\FileType::class     => InvokableFactory::class,
                Helper\FormGroup::class    => InvokableFactory::class,
                Helper\Icon::class         => Helper\Factory\IconFactory::class,
                Helper\Image::class        => Helper\Factory\ImageFactory::class,
                Helper\ResourceLink::class => InvokableFactory::class,
                Helper\RichContent::class  => InvokableFactory::class,
                Helper\Settings::class     => Helper\Factory\SettingsFactory::class,
                Helper\SocialLink::class   => InvokableFactory::class,
                Helper\Srcset::class       => InvokableFactory::class,
                Helper\Truncate::class     => InvokableFactory::class,
                Helper\UrlFormat::class    => InvokableFactory::class,
                Helper\Video::class        => InvokableFactory::class,
            ],
        ];
    }

    /**
     * @return array{view_helpers: array{aliases: array<string, class-string>, factories: array<class-string, class-string>}}
     */
    public function __invoke(): array
    {
        return [
            'view_helpers' => $this->getViewHelperConfig(),
        ];
    }
}
