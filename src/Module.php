<?php

namespace Contenir\View;

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
                    'acl'         => Helper\Acl::class,
                    'Acl'         => Helper\Acl::class,
                    'cache'       => Helper\Cache::class,
                    'Cache'       => Helper\Cache::class,
                    'dateFormat'  => Helper\DateFormat::class,
                    'DateFormat'  => Helper\DateFormat::class,
                    'escapeEmail' => Helper\EscapeEmail::class,
                    'EscapeEmail' => Helper\EscapeEmail::class,
                    'fileSize'    => Helper\FileSize::class,
                    'FileSize'    => Helper\FileSize::class,
                    'fileType'    => Helper\FileType::class,
                    'FileType'    => Helper\FileType::class,
                    'formGroup'   => Helper\FormGroup::class,
                    'FormGroup'   => Helper\FormGroup::class,
                    'icon'        => Helper\Icon::class,
                    'Icon'        => Helper\Icon::class,
                    'image'       => Helper\Image::class,
                    'Image'       => Helper\Image::class,
                    'richContent' => Helper\RichContent::class,
                    'RichContent' => Helper\RichContent::class,
                    'settings'    => Helper\Settings::class,
                    'Settings'    => Helper\Settings::class,
                    'socialLink'  => Helper\SocialLink::class,
                    'SocialLink'  => Helper\SocialLink::class,
                    'srcset'      => Helper\Srcset::class,
                    'Srcset'      => Helper\Srcset::class,
                    'truncate'    => Helper\Truncate::class,
                    'Truncate'    => Helper\Truncate::class,
                    'urlFormat'   => Helper\UrlFormat::class,
                    'UrlFormat'   => Helper\UrlFormat::class,
                    'video'   	  => Helper\Video::class,
                    'Video'       => Helper\Video::class,
                ],
                'factories' => [
                    Helper\Acl::class         => Helper\Factory\AclFactory::class,
                    Helper\Cache::class       => Helper\Factory\CacheFactory::class,
                    Helper\DateFormat::class  => InvokableFactory::class,
                    Helper\EscapeEmail::class => InvokableFactory::class,
                    Helper\FileSize::class    => InvokableFactory::class,
                    Helper\FileType::class    => InvokableFactory::class,
                    Helper\FormGroup::class   => InvokableFactory::class,
                    Helper\Icon::class        => InvokableFactory::class,
                    Helper\Image::class       => Helper\Factory\ImageFactory::class,
                    Helper\RichContent::class => InvokableFactory::class,
                    Helper\Settings::class    => Helper\Factory\SettingsFactory::class,
                    Helper\SocialLink::class  => InvokableFactory::class,
                    Helper\Srcset::class      => InvokableFactory::class,
                    Helper\Truncate::class    => InvokableFactory::class,
                    Helper\UrlFormat::class   => InvokableFactory::class,
                    Helper\Video::class       => InvokableFactory::class,
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
            ]
        ];
    }
}
