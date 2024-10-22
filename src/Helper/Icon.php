<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class Icon extends AbstractHelper
{
    protected string $tag ='i';
    protected ?string $class = null;
    protected string $basePath = './public/asset/icon';
    protected string $extension = 'svg';

    public function __invoke($iconName, array $options = []): bool|string
    {
        $this->setTag($options['tag'] ?? $this->tag);
        $this->setClass($options['tag'] ?? $this->class);
        $this->setBasePath($options['base_path'] ?? $this->basePath);
        $this->setExtension($options['extension'] ?? $this->extension);

        $iconPath = sprintf(
            '%s/%s.%s',
            $this->getBasePath(),
            $iconName,
            $this->getExtension()
        );

        $iconData = (file_exists($iconPath)) ? (string)file_get_contents($iconPath) : '';

        if (! empty($this->getClass())) {
            $iconData = sprintf(
                '<%s class="%s">%s</%s>',
                $this->getTag(),
                $this->getClass(),
                $iconData,
                $this->getTag()
            );
        }

        return $iconData;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;
        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass($className): self
    {
        $this->class = $className;
        return $this;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function setBasePath($basePath): self
    {
        $this->basePath = $basePath;
        return $this;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setExtension($extension): self
    {
        $this->extension = $extension;
        return $this;
    }
}
