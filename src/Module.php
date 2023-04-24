<?php

namespace Contenir\View;

use Contenir\Asset\AssetManager;
use Laminas\ServiceManager\Factory\InvokableFactory;

class Module
{
    /**
     * Retrieve default laminas-paginator config for laminas-mvc context.
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'view_helpers' => [
                'aliases' => [
                    'acl'             => Helper\Acl::class,
                    'Acl'             => Helper\Acl::class,
                    'asset'           => Helper\Asset::class,
                    'Asset'           => Helper\Asset::class,
                    'assetSrc'        => Helper\AssetSrc::class,
                    'AssetSrc'        => Helper\AssetSrc::class,
                    'assetSrcSet'     => Helper\AssetSrcSet::class,
                    'AssetSrcSet'     => Helper\AssetSrcSet::class,
                    'assetUrl'        => Helper\AssetUrl::class,
                    'AssetUrl'        => Helper\AssetUrl::class,
                    'cache'           => Helper\Cache::class,
                    'Cache'           => Helper\Cache::class,
                    'collection'      => Helper\Collection::class,
                    'Collection'      => Helper\Collection::class,
                    'dateFormat'      => Helper\DateFormat::class,
                    'DateFormat'      => Helper\DateFormat::class,
                    'escapeEmail'     => Helper\EscapeEmail::class,
                    'EscapeEmail'     => Helper\EscapeEmail::class,
                    'fileSize'        => Helper\FileSize::class,
                    'FileSize'        => Helper\FileSize::class,
                    'fileType'        => Helper\FileType::class,
                    'FileType'        => Helper\FileType::class,
                    'formGroup'       => Helper\FormGroup::class,
                    'FormGroup'       => Helper\FormGroup::class,
                    'icon'            => Helper\Icon::class,
                    'Icon'            => Helper\Icon::class,
                    'image'           => Helper\Image::class,
                    'Image'           => Helper\Image::class,
                    'resource'        => Helper\Resource::class,
                    'Resource'        => Helper\Resource::class,
                    'resourceContent' => Helper\ResourceContent::class,
                    'ResourceContent' => Helper\ResourceContent::class,
                    'resourceMeta'    => Helper\ResourceMeta::class,
                    'ResourceMeta'    => Helper\ResourceMeta::class,
                    'settings'        => Helper\Settings::class,
                    'Settings'        => Helper\Settings::class,
                    'socialLink'      => Helper\SocialLink::class,
                    'SocialLink'      => Helper\SocialLink::class,
                    'srcset'          => Helper\Srcset::class,
                    'Srcset'          => Helper\Srcset::class,
                    'truncate'        => Helper\Truncate::class,
                    'Truncate'        => Helper\Truncate::class,
                    'urlFormat'       => Helper\UrlFormat::class,
                    'UrlFormat'       => Helper\UrlFormat::class,
                ],
                'factories' => [
                    Helper\Acl::class             => Helper\Factory\AclFactory::class,
                    Helper\Asset::class           => Helper\Factory\AssetFactory::class,
                    Helper\AssetSrc::class        => Helper\Factory\AssetSrcSetFactory::class,
                    Helper\AssetSrcSet::class     => Helper\Factory\AssetSrcSetFactory::class,
                    Helper\AssetUrl::class        => Helper\Factory\AssetUrlFactory::class,
                    Helper\Cache::class           => Helper\Factory\CacheFactory::class,
                    Helper\Collection::class      => Helper\Factory\CollectionFactory::class,
                    Helper\DateFormat::class      => InvokableFactory::class,
                    Helper\EscapeEmail::class     => InvokableFactory::class,
                    Helper\FileSize::class        => InvokableFactory::class,
                    Helper\FileType::class        => InvokableFactory::class,
                    Helper\FormGroup::class       => InvokableFactory::class,
                    Helper\Icon::class            => InvokableFactory::class,
                    Helper\Image::class           => Helper\Factory\ImageFactory::class,
                    Helper\Resource::class        => Helper\Factory\ResourceFactory::class,
                    Helper\ResourceContent::class => InvokableFactory::class,
                    Helper\ResourceMeta::class    => InvokableFactory::class,
                    Helper\Settings::class        => Helper\Factory\SettingsFactory::class,
                    Helper\SocialLink::class      => InvokableFactory::class,
                    Helper\Srcset::class          => InvokableFactory::class,
                    Helper\Truncate::class        => InvokableFactory::class,
                    Helper\UrlFormat::class       => InvokableFactory::class,
                ],
            ],
            'view_manager' => [
                'display_not_found_reason' => true,
                'display_exceptions'       => true,
                'doctype'                  => 'HTML5',
                'not_found_template'       => 'error/404',
                'forbidden_template'       => 'error/403',
                'exception_template'       => 'error/index',
                'strategies'               => [
                    'ViewJsonStrategy',
                ]
            ],
            'view_asset' => [
                'asset_manager' => AssetManager::class
            ],
            'view_srcset' => [
                'sizes' => [
                    'small' => [
                        640  => 320,
                        960  => 480,
                        1440 => 720
                    ],
                    'medium' => [
                        640  => 480,
                        960  => 720,
                        1440 => 1080,
                        2400 => 1800
                    ],
                    'large' => [
                        640  => 640,
                        960  => 960,
                        1440 => 1440,
                        2400 => 2400
                    ]
                ],
                'helper' => '/usr/local/bin/convert'
            ]
        ];
    }
}
