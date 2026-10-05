<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;

use function file_get_contents;
use function is_file;
use function sprintf;

/**
 * Inlines an SVG icon from disk, optionally wrapped in an element carrying
 * a class. Per-call options override the helper's defaults for that call
 * only.
 *
 * @api
 */
final class Icon extends AbstractHelper
{
    private string  $tag       = 'i';
    private ?string $class     = null;
    private string  $basePath  = './public/asset/icon';
    private string  $extension = 'svg';

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function setBasePath(string $basePath): self
    {
        $this->basePath = $basePath;

        return $this;
    }

    public function setClass(?string $className): self
    {
        $this->class = $className;

        return $this;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @param array{tag?: string, class?: string|null, base_path?: string, extension?: string} $options
     *
     * @return string The file's contents, wrapped when a class is set; empty when the file is missing.
     */
    public function __invoke(string $iconName, array $options = []): string
    {
        $tag      = $options['tag'] ?? $this->tag;
        $class    = $options['class'] ?? $this->class;
        $iconPath = sprintf(
            '%s/%s.%s',
            $options['base_path'] ?? $this->basePath,
            $iconName,
            $options['extension'] ?? $this->extension,
        );

        $iconData = is_file($iconPath) ? (string) file_get_contents($iconPath) : '';

        if (null === $class || '' === $class) {
            return $iconData;
        }

        return sprintf('<%1$s class="%2$s">%3$s</%1$s>', $tag, $class, $iconData);
    }
}
