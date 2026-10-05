<?php

declare(strict_types=1);

namespace Contenir\View;

/**
 * laminas-mvc module: the {@see ConfigProvider} view helpers, plus the
 * view manager defaults Contenir sites share.
 *
 * @api
 */
final class Module
{
    /**
     * @return array{
     *     view_helpers: array{aliases: array<string, class-string>, factories: array<class-string, class-string>},
     *     view_manager: array<string, mixed>,
     * }
     */
    public function getConfig(): array
    {
        return [
            ...(new ConfigProvider())(),
            'view_manager' => [
                'display_not_found_reason' => true,
                'display_exceptions'       => true,
                'doctype'                  => 'HTML5',
                'not_found_template'       => 'error/404',
                'forbidden_template'       => 'error/403',
                'exception_template'       => 'error/index',
                'strategies'               => [
                    'ViewJsonStrategy',
                ],
            ],
        ];
    }
}
