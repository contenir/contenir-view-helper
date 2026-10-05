<?php

declare(strict_types=1);

namespace Contenir\View\Helper\Factory;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function is_array;

/**
 * Reads one section of the application's "config" service, treating a
 * missing or non-array section as empty.
 *
 * @internal
 */
final readonly class ConfigReader
{
    /**
     * @return array<array-key, mixed>
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @mago-expect analysis:mixed-assignment Configuration is untyped input; validated here.
     */
    public static function section(ContainerInterface $container, string ...$path): array
    {
        $config = $container->get('config');

        foreach ($path as $key) {
            $config = is_array($config) ? $config[$key] ?? [] : [];
        }

        return is_array($config) ? $config : [];
    }
}
