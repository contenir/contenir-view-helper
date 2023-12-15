<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Traversable;

class Icon extends AbstractHelper
{
    protected $options = [
        'class'     => 'icon',
        'base_path' => './public/asset/icon',
        'extension' => 'svg'
    ];

    public function __invoke($iconName, array $options = [])
    {
        $options = array_merge($this->options, $options);

        $iconPath = sprintf(
            '%s/%s.%s',
            $options['base_path'],
            $iconName,
            $options['extension']
        );

        $iconData = file_get_contents($iconPath);

        return sprintf(
            '<i class="%s">%s</i>',
            $options['class'],
            $iconData
        );
    }

    public function getClass()
    {
        return $this->options['class'];
    }

    public function setClass($className)
    {
        $this->options['class'] = $className;
    }

    public function getBasePath()
    {
        return $this->options['base_path'];
    }

    public function setBasePath($basePath)
    {
        $this->options['base_path'] = $basePath;
    }

    public function getExtension()
    {
        return $this->options['extension'];
    }

    public function setExtension($extension)
    {
        $this->options['extension'] = $extension;
    }
}
